@extends('layouts.app')

@section('title', 'Attendance Form')

@section('header-title', 'Employee Attendance')

@section('content')
<div class="mx-auto max-w-4xl">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-6 bg-blue-600 text-white">
            <h2 class="text-2xl font-bold">Welcome, {{ $employee->name }}</h2>
            <p class="text-blue-100">Employee ID: {{ $employee->employee_id }} | Department: {{ $employee->department->name }}</p>
            <p class="mt-1">{{ now()->format('l, F d, Y') }}</p>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-4">
                    <div class="relative">
                        <video id="video" class="w-full h-auto rounded-lg border border-gray-300" autoplay muted></video>
                        <canvas id="canvas" class="absolute top-0 left-0 w-full h-full" style="display: none;"></canvas>
                        <div id="face-status" class="absolute bottom-2 left-2 bg-gray-800 bg-opacity-75 text-white px-2 py-1 rounded text-sm">
                            Initializing camera...
                        </div>
                    </div>
                    
                    <div class="flex justify-between space-x-4">
                        <button id="capture-btn" class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                            Capture Photo
                        </button>
                        <button id="retry-btn" class="flex-1 bg-gray-300 text-gray-800 py-2 px-4 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 hidden">
                            Retry
                        </button>
                    </div>
                </div>
                
                <div>
                    <div class="mb-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Attendance Status</h3>
                        
                        @php
                            $attendance = $employee->todayAttendance();
                        @endphp
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 border rounded-lg {{ $attendance && $attendance->clock_in ? 'border-green-500 bg-green-50' : 'border-gray-300' }}">
                                <p class="text-sm text-gray-500">Clock In</p>
                                <p class="font-semibold">
                                    @if($attendance && $attendance->clock_in)
                                        {{ \Carbon\Carbon::parse($attendance->clock_in)->format('H:i:s') }}
                                    @else
                                        Not yet
                                    @endif
                                </p>
                            </div>
                            <div class="p-4 border rounded-lg {{ $attendance && $attendance->clock_out ? 'border-green-500 bg-green-50' : 'border-gray-300' }}">
                                <p class="text-sm text-gray-500">Clock Out</p>
                                <p class="font-semibold">
                                    @if($attendance && $attendance->clock_out)
                                        {{ \Carbon\Carbon::parse($attendance->clock_out)->format('H:i:s') }}
                                    @else
                                        Not yet
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Record Attendance</h3>
                        
                        <div class="space-y-4">
                            @if(!$attendance || !$attendance->clock_in)
                                <form id="clock-in-form" action="{{ route('attendance.clock-in') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="employee_id" value="{{ $employee->employee_id }}">
                                    <input type="hidden" name="photo" id="clock-in-photo">
                                    
                                    <button type="button" id="clock-in-btn" class="w-full bg-green-600 text-white py-3 px-4 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                        Clock In
                                    </button>
                                </form>
                            @elseif(!$attendance->clock_out)
                                <form id="clock-out-form" action="{{ route('attendance.clock-out') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="employee_id" value="{{ $employee->employee_id }}">
                                    <input type="hidden" name="photo" id="clock-out-photo">
                                    
                                    <button type="button" id="clock-out-btn" class="w-full bg-red-600 text-white py-3 px-4 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                        Clock Out
                                    </button>
                                </form>
                            @else
                                <div class="p-4 bg-gray-100 rounded-lg text-center">
                                    <p class="text-gray-700">You have completed your attendance for today.</p>
                                </div>
                            @endif
                            
                            <a href="{{ route('attendance.index') }}" class="block text-center w-full bg-gray-300 text-gray-800 py-2 px-4 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 mt-4">
                                Back to Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Store employee face data if available
    const employeeFaceData = @json($employee->face_data ? json_decode($employee->face_data) : null);
    
    // Elements
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const captureBtn = document.getElementById('capture-btn');
    const retryBtn = document.getElementById('retry-btn');
    const faceStatus = document.getElementById('face-status');
    const clockInBtn = document.getElementById('clock-in-btn');
    const clockOutBtn = document.getElementById('clock-out-btn');
    
    // Face detection variables
    let isModelLoaded = false;
    let isCaptured = false;
    let currentStream = null;
    let detectionInterval = null;
    let faceDetected = false;
    
    // Initialize face detection
    async function initFaceDetection() {
        try {
            faceStatus.textContent = 'Loading face detection...';
            
            // Coba gunakan fungsi eksternal jika tersedia
            if (window.faceApiInit && typeof window.faceApiInit === 'function') {
                console.log('Using external face-api init function');
                const success = await window.faceApiInit();
                if (!success) {
                    console.warn('External init failed, falling back to direct model loading');
                }
            } else {
                console.log('Using direct model loading');
                // Coba deteksi jenis API yang tersedia
                if (typeof faceapi.nets !== 'undefined') {
                    // API style lama
                    await faceapi.nets.tinyFaceDetector.loadFromUri('/models');
                    await faceapi.nets.faceLandmark68Net.loadFromUri('/models');
                    console.log('Models loaded using legacy API');
                } else {
                    // API style baru
                    await faceapi.loadTinyFaceDetectorModel('/models');
                    await faceapi.loadFaceLandmarkModel('/models');
                    console.log('Models loaded using modern API');
                }
            }
            
            isModelLoaded = true;
            faceStatus.textContent = 'Face detection ready';
            
            // Start webcam
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: 'user' } 
                });
                
                video.srcObject = stream;
                currentStream = stream;
                
                // Start face detection
                startFaceDetection();
            } catch (cameraError) {
                console.error('Camera error:', cameraError);
                faceStatus.textContent = `Camera error: ${cameraError.message}`;
                faceStatus.className = 'absolute bottom-2 left-2 bg-red-600 bg-opacity-75 text-white px-2 py-1 rounded text-sm';
            }
        } catch (error) {
            console.error('Error initializing face detection:', error);
            faceStatus.textContent = 'Camera access denied or not available';
            faceStatus.className = 'absolute bottom-2 left-2 bg-red-600 bg-opacity-75 text-white px-2 py-1 rounded text-sm';
        }
    }
    
    // Start face detection loop
    function startFaceDetection() {
        if (detectionInterval) clearInterval(detectionInterval);
        
        detectionInterval = setInterval(async () => {
            if (!isModelLoaded || isCaptured) return;
            
            try {
                let detections;
                
                // Cek jenis API yang tersedia
                if (typeof faceapi.detectSingleFace === 'function') {
                    // API baru (seperti di admin)
                    detections = await faceapi.detectSingleFace(
                        video, 
                        new faceapi.TinyFaceDetectorOptions()
                    );
                } else if (typeof faceapi.nets !== 'undefined') {
                    // API lama 
                    detections = await faceapi.detectSingleFace(
                        video, 
                        new faceapi.TinyFaceDetectorOptions()
                    );
                } else {
                    console.warn('No supported face detection method found');
                    return;
                }
                
                if (detections) {
                    faceStatus.textContent = 'Face detected';
                    faceStatus.className = 'absolute bottom-2 left-2 bg-green-600 bg-opacity-75 text-white px-2 py-1 rounded text-sm';
                    faceDetected = true;
                    captureBtn.disabled = false;
                    
                    // Enable clock in/out buttons if they exist
                    if (clockInBtn) clockInBtn.disabled = false;
                    if (clockOutBtn) clockOutBtn.disabled = false;
                } else {
                    faceStatus.textContent = 'No face detected';
                    faceStatus.className = 'absolute bottom-2 left-2 bg-gray-800 bg-opacity-75 text-white px-2 py-1 rounded text-sm';
                    faceDetected = false;
                    captureBtn.disabled = true;
                    
                    // Disable clock in/out buttons if they exist
                    if (clockInBtn) clockInBtn.disabled = true;
                    if (clockOutBtn) clockOutBtn.disabled = true;
                }
            } catch (error) {
                console.error('Face detection error:', error);
                faceStatus.textContent = 'Face detection error';
                faceStatus.className = 'absolute bottom-2 left-2 bg-red-600 bg-opacity-75 text-white px-2 py-1 rounded text-sm';
            }
        }, 500);
    }
    
    // Capture photo
    captureBtn.addEventListener('click', () => {
        if (!faceDetected) return;
        
        const context = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        canvas.style.display = 'block';
        video.style.display = 'none';
        captureBtn.style.display = 'none';
        retryBtn.style.display = 'block';
        
        isCaptured = true;
        
        // Convert capture to blob and then to file input
        canvas.toBlob(blob => {
            const file = new File([blob], 'attendance-photo.jpg', { type: 'image/jpeg' });
            
            // Create a FileList-like object
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            
            // Assign to the correct form input
            if (clockInBtn) {
                const photoInput = document.getElementById('clock-in-photo');
                photoInput.files = dataTransfer.files;
                clockInBtn.disabled = false;
            }
            
            if (clockOutBtn) {
                const photoInput = document.getElementById('clock-out-photo');
                photoInput.files = dataTransfer.files;
                clockOutBtn.disabled = false;
            }
        }, 'image/jpeg', 0.9);
    });
    
    // Retry capture
    retryBtn.addEventListener('click', () => {
        canvas.style.display = 'none';
        video.style.display = 'block';
        captureBtn.style.display = 'block';
        retryBtn.style.display = 'none';
        
        isCaptured = false;
        
        // Disable buttons again
        if (clockInBtn) clockInBtn.disabled = true;
        if (clockOutBtn) clockOutBtn.disabled = true;
    });
    
    // Submit attendance
    if (clockInBtn) {
        clockInBtn.addEventListener('click', function() {
            if (isCaptured) {
                document.getElementById('clock-in-form').submit();
            }
        });
    }
    
    if (clockOutBtn) {
        clockOutBtn.addEventListener('click', function() {
            if (isCaptured) {
                document.getElementById('clock-out-form').submit();
            }
        });
    }
    
    // Initialize face detection when page loads
    window.addEventListener('DOMContentLoaded', initFaceDetection);
    
    // Clean up when page unloads
    window.addEventListener('beforeunload', () => {
        if (currentStream) {
            currentStream.getTracks().forEach(track => track.stop());
        }
        
        if (detectionInterval) {
            clearInterval(detectionInterval);
        }
    });
</script>
@endsection 