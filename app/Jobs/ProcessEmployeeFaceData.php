<?php

namespace App\Jobs;

use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ProcessEmployeeFaceData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $employee;
    protected $photoPath;
    
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 2;
    
    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(Employee $employee, string $photoPath)
    {
        $this->employee = $employee;
        $this->photoPath = $photoPath;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Use a simple PHP script with face-api.js in Node environment
            // Create a temporary script file to process the image
            $scriptContent = $this->createNodeScript();
            $scriptPath = storage_path('app/temp_face_script_' . uniqid() . '.js');
            file_put_contents($scriptPath, $scriptContent);
            
            // Execute Node.js script
            $process = new Process([
                'node', 
                $scriptPath, 
                $this->photoPath, 
                $this->employee->id
            ]);
            
            $process->setTimeout(60);
            $process->run();
            
            // Check if the process was successful
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
            
            // Parse the output
            $output = json_decode($process->getOutput(), true);
            
            if (isset($output['success']) && $output['success']) {
                // Update employee face data
                $this->employee->update([
                    'face_data' => json_encode($output['data']),
                ]);
                
                Log::info("Face data processed successfully for employee {$this->employee->name} (ID: {$this->employee->employee_id})");
            } else {
                Log::error("Failed to process face data: " . ($output['message'] ?? 'Unknown error'));
            }
            
            // Clean up
            @unlink($scriptPath);
            
        } catch (\Exception $e) {
            Log::error("Error processing face data: {$e->getMessage()}");
            Log::error($e->getTraceAsString());
            
            // Cleanup
            if (isset($scriptPath) && file_exists($scriptPath)) {
                @unlink($scriptPath);
            }
            
            throw $e;
        }
    }
    
    /**
     * Create the Node.js script content for processing the image
     */
    protected function createNodeScript(): string
    {
        // This script will use face-api.js in Node environment
        return <<<JAVASCRIPT
const fs = require('fs');
const path = require('path');
const canvas = require('canvas');
const faceapi = require('face-api.js');

// Add canvas to Node.js environment
const { Canvas, Image, ImageData } = canvas;
faceapi.env.monkeyPatch({ Canvas, Image, ImageData });

async function processImage() {
    try {
        const imagePath = process.argv[2];
        const employeeId = process.argv[3];
        
        if (!imagePath || !employeeId) {
            console.log(JSON.stringify({
                success: false,
                message: 'Missing arguments: imagePath and employeeId required'
            }));
            process.exit(1);
        }
        
        // Load the models
        const modelsPath = path.join(__dirname, '../public/models');
        await faceapi.nets.ssdMobilenetv1.loadFromDisk(modelsPath);
        await faceapi.nets.faceLandmark68Net.loadFromDisk(modelsPath);
        await faceapi.nets.faceRecognitionNet.loadFromDisk(modelsPath);
        
        // Load the image
        const img = await canvas.loadImage(imagePath);
        
        // Detect faces in the image
        const detections = await faceapi.detectAllFaces(img)
            .withFaceLandmarks()
            .withFaceDescriptors();
        
        if (detections.length === 0) {
            console.log(JSON.stringify({
                success: false,
                message: 'No face detected in the image'
            }));
            process.exit(1);
        }
        
        if (detections.length > 1) {
            console.log(JSON.stringify({
                success: false,
                message: 'Multiple faces detected in the image'
            }));
            process.exit(1);
        }
        
        // Get the first detected face
        const detection = detections[0];
        
        // Prepare the face data
        const faceData = {
            detection: {
                box: detection.detection.box,
                score: detection.detection.score
            },
            landmarks: {
                positions: detection.landmarks.positions.map(p => ({ x: p.x, y: p.y }))
            },
            descriptor: Array.from(detection.descriptor),
            timestamp: Date.now()
        };
        
        // Return the results
        console.log(JSON.stringify({
            success: true,
            data: faceData
        }));
        
    } catch (error) {
        console.log(JSON.stringify({
            success: false,
            message: error.message
        }));
        process.exit(1);
    }
}

processImage();
JAVASCRIPT;
    }
} 