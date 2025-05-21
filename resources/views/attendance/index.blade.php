@extends('layouts.app')

@section('title', 'Employee Attendance')

@section('header-title', 'Employee Attendance System')

@section('content')
<div class="mx-auto max-w-4xl">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-6 bg-blue-600 text-white text-center">
            <h2 class="text-2xl font-bold">Employee Attendance</h2>
            <p class="mt-2">{{ now()->format('l, F d, Y') }}</p>
            <div class="text-3xl font-bold mt-2" id="clock">{{ now()->format('H:i:s') }}</div>
        </div>
        
        <div class="p-6">
            <div class="space-y-6">
                <!-- Face Recognition Section -->
                <div class="space-y-4">
                    <div class="relative">
                        <video id="video" class="w-full h-auto rounded-lg border border-gray-300" autoplay muted></video>
                        <canvas id="overlay" class="absolute top-0 left-0 w-full h-full"></canvas>
                        <div id="face-status" class="absolute bottom-2 left-2 bg-gray-800 bg-opacity-75 text-white px-2 py-1 rounded text-sm">
                            Initializing camera...
                        </div>
                    </div>
                    
                    <div id="recognition-status" class="hidden p-4 rounded-lg text-center">
                        <!-- Will be filled by JS when a face is recognized -->
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <button id="clock-in-btn" class="bg-green-600 text-white py-3 px-4 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                            Clock In
                        </button>
                        <button id="clock-out-btn" class="bg-red-600 text-white py-3 px-4 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                            Clock Out
                        </button>
                    </div>
                </div>
                
                <!-- Manual ID Entry (as fallback) -->
                <div class="pt-6 border-t border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Manual Entry</h3>
                        <button id="toggle-manual" class="text-sm text-blue-600 hover:text-blue-800">
                            Show Manual Entry
                        </button>
                    </div>
                    
                    <form id="manual-form" action="{{ route('attendance.validate') }}" method="POST" class="hidden">
                        @csrf
                        <div class="mb-4">
                            <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-2">Employee ID</label>
                            <input type="text" id="employee_id" name="employee_id" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Enter your employee ID" required>
                            @error('employee_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="flex justify-center">
                            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Continue with ID
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Forms for Face Recognition -->
<form id="face-clock-in-form" action="{{ route('attendance.clock-in') }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="employee_id" id="face-clock-in-employee-id">
    <input type="hidden" name="photo" id="face-clock-in-photo">
</form>

<form id="face-clock-out-form" action="{{ route('attendance.clock-out') }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="employee_id" id="face-clock-out-employee-id">
    <input type="hidden" name="photo" id="face-clock-out-photo">
</form>
@endsection

@section('scripts')
<script>
    // Global error handler
    window.addEventListener('error', function(e) {
        console.error('Global error caught:', e.error);
        // Add error to the page for debugging
        const errorDiv = document.createElement('div');
        errorDiv.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4';
        errorDiv.innerHTML = `<strong>JavaScript Error:</strong> ${e.message}<br>Line: ${e.lineno}, File: ${e.filename}`;
        document.querySelector('main').prepend(errorDiv);
    });
    
    // Clock functionality
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        document.getElementById('clock').textContent = `${hours}:${minutes}:${seconds}`;
    }
    
    setInterval(updateClock, 1000);
    updateClock();
    
    // Face Recognition Variables
    const video = document.getElementById('video');
    const overlay = document.getElementById('overlay');
    const faceStatus = document.getElementById('face-status');
    const recognitionStatus = document.getElementById('recognition-status');
    const clockInBtn = document.getElementById('clock-in-btn');
    const clockOutBtn = document.getElementById('clock-out-btn');
    
    let isModelLoaded = false;
    let currentStream = null;
    let detectionInterval = null;
    let recognizedEmployee = null;
    
    // Fetch all employees data for face recognition
    let employeesData = [];
    
    // Initialize face detection
    async function initFaceRecognition() {
        try {
            faceStatus.textContent = 'Loading face detection models...';
            
            // Tunggu library dimuat
            if (typeof faceapi === 'undefined') {
                console.log('Waiting for face-api.js to load...');
                await new Promise(resolve => setTimeout(resolve, 1000));
                if (typeof faceapi === 'undefined') {
                    throw new Error('face-api.js failed to load after waiting');
                }
            }
            
            // Coba gunakan fungsi inisialisasi dari file eksternal
            try {
                if (window.faceApiInit && typeof window.faceApiInit === 'function') {
                    console.log('Using external face-api init function');
                    const success = await window.faceApiInit();
                    if (!success) {
                        console.warn('External init failed, falling back to direct loading');
                        // Fallback ke loading langsung jika fungsi eksternal gagal
                        if (typeof faceapi.nets !== 'undefined') {
                            // API style lama (halaman attendance)
                            await faceapi.nets.tinyFaceDetector.loadFromUri('/models');
                            await faceapi.nets.faceLandmark68Net.loadFromUri('/models');
                            console.log('Models loaded using legacy API');
                        } else {
                            // API style baru (admin)
                            await faceapi.loadTinyFaceDetectorModel('/models');
                            await faceapi.loadFaceLandmarkModel('/models');
                            console.log('Models loaded using modern API');
                        }
                    }
                } else {
                    // Load model langsung jika fungsi eksternal tidak tersedia
                    console.log('External init not available, using direct loading');
                    if (typeof faceapi.nets !== 'undefined') {
                        // API style lama (halaman attendance)
                        await faceapi.nets.tinyFaceDetector.loadFromUri('/models');
                        await faceapi.nets.faceLandmark68Net.loadFromUri('/models');
                        console.log('Models loaded using legacy API');
                    } else {
                        // API style baru (admin)
                        await faceapi.loadTinyFaceDetectorModel('/models');
                        await faceapi.loadFaceLandmarkModel('/models');
                        console.log('Models loaded using modern API');
                    }
                }
                
                isModelLoaded = true;
                faceStatus.textContent = 'Face models loaded. Starting camera...';
                
                // Load employee data for recognition
                await loadEmployeesData();
                
                // Start webcam
                try {
                    const constraints = { 
                        video: { 
                            facingMode: 'user',
                            width: { ideal: 640 },
                            height: { ideal: 480 }
                        } 
                    };
                    
                    console.log('Requesting camera with constraints:', constraints);
                    const stream = await navigator.mediaDevices.getUserMedia(constraints);
                    
                    video.srcObject = stream;
                    currentStream = stream;
                    
                    video.onloadedmetadata = () => {
                        console.log(`Video dimensions: ${video.videoWidth}x${video.videoHeight}`);
                        // Setup overlay canvas
                        overlay.width = video.videoWidth;
                        overlay.height = video.videoHeight;
                    };
                    
                    // When video is playing, start detection
                    video.addEventListener('play', () => {
                        console.log('Video started playing');
                        // Set canvas dimensions
                        overlay.width = video.videoWidth;
                        overlay.height = video.videoHeight;
                        
                        // Start face detection loop
                        startFaceDetection();
                    });
                    
                    faceStatus.textContent = 'Camera ready. Looking for face...';
                } catch (cameraError) {
                    console.error('Detailed camera error:', cameraError.name, cameraError.message);
                    
                    if (cameraError.name === 'NotAllowedError') {
                        faceStatus.textContent = 'Camera access denied. Please allow camera access and reload.';
                    } else if (cameraError.name === 'NotFoundError') {
                        faceStatus.textContent = 'No camera detected on this device.';
                    } else if (cameraError.name === 'NotReadableError') {
                        faceStatus.textContent = 'Camera is already in use by another application.';
                    } else if (cameraError.name === 'OverconstrainedError') {
                        faceStatus.textContent = 'Camera constraints cannot be satisfied.';
                    } else if (location.protocol === 'http:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
                        faceStatus.textContent = 'Camera requires HTTPS. Try using localhost or enable HTTPS.';
                    } else {
                        faceStatus.textContent = `Camera error: ${cameraError.message}`;
                    }
                    
                    faceStatus.className = 'absolute bottom-2 left-2 bg-red-600 bg-opacity-75 text-white px-2 py-1 rounded text-sm';
                    
                    // Show manual entry option as fallback
                    document.getElementById('toggle-manual').click();
                }
            } catch (error) {
                console.error('Error initializing face recognition:', error);
                faceStatus.textContent = 'Face detection initialization failed';
                faceStatus.className = 'absolute bottom-2 left-2 bg-red-600 bg-opacity-75 text-white px-2 py-1 rounded text-sm';
                // Show manual entry option as fallback
                document.getElementById('toggle-manual').click();
            }
        } catch (error) {
            console.error('Error initializing face recognition:', error);
            faceStatus.textContent = 'Face detection initialization failed';
            faceStatus.className = 'absolute bottom-2 left-2 bg-red-600 bg-opacity-75 text-white px-2 py-1 rounded text-sm';
            // Show manual entry option as fallback
            document.getElementById('toggle-manual').click();
        }
    }
    
    // Load employee data from server
    async function loadEmployeesData() {
        try {
            console.log('Fetching employee face data from API...');
            const response = await fetch('/api/employees-face-data');
            
            if (response.ok) {
                employeesData = await response.json();
                console.log(`Loaded ${employeesData.length} employee profiles for face recognition`);
                
                if (employeesData.length === 0) {
                    console.warn('No employee data returned from API');
                } else {
                    // Log sample data without sensitive information
                    const sampleEmployee = {...employeesData[0]};
                    if (sampleEmployee.face_data) {
                        sampleEmployee.face_data = '(DATA AVAILABLE)';
                    }
                    console.log('Sample employee data:', sampleEmployee);
                }
            } else {
                const errorText = await response.text();
                console.error(`Failed to load employee face data: ${response.status} ${response.statusText}`, errorText);
            }
        } catch (error) {
            console.error('Error loading employee data:', error);
        }
    }
    
    // Start face detection loop
    function startFaceDetection() {
        if (detectionInterval) clearInterval(detectionInterval);
        
        detectionInterval = setInterval(async () => {
            if (!isModelLoaded || !video.videoWidth) return;
            
            try {
                // Gunakan SSD MobileNet untuk deteksi yang lebih akurat
                // Add face recognition descriptors to enable proper face comparison
                const results = await faceapi.detectAllFaces(video,
                    new faceapi.SsdMobilenetv1Options({ minConfidence: 0.5 })
                )
                .withFaceLandmarks()
                .withFaceDescriptors(); // Add face descriptors for comparison
                    
                const ctx = overlay.getContext('2d');
                ctx.clearRect(0, 0, overlay.width, overlay.height);
                
                if (results.length > 0) {
                    faceStatus.textContent = 'Face detected! Identifying...';
                    faceStatus.className = 'absolute bottom-2 left-2 bg-green-600 bg-opacity-75 text-white px-2 py-1 rounded text-sm';
                    
                    // Gambar hasil deteksi
                    faceapi.draw.drawDetections(overlay, results);
                    faceapi.draw.drawFaceLandmarks(overlay, results);
                        
                    // Use proper face identification instead of just selecting the first employee
                    identifyEmployee(results[0].descriptor);
                } else {
                    faceStatus.textContent = 'No face detected';
                    faceStatus.className = 'absolute bottom-2 left-2 bg-gray-800 bg-opacity-75 text-white px-2 py-1 rounded text-sm';
                    recognizedEmployee = null;
                    recognitionStatus.classList.add('hidden');
                    clockInBtn.disabled = true;
                    clockOutBtn.disabled = true;
                }
            } catch (detectError) {
                console.error('Face detection error:', detectError);
                faceStatus.textContent = 'Face detection error';
            }
        }, 500);
    }
    
    // Proper face identification comparing face descriptors
    function identifyEmployee(faceDescriptor) {
        if (employeesData.length === 0) {
            console.log('No employee data available for face recognition');
            faceStatus.textContent = 'No employee data available. Use manual entry.';
            faceStatus.className = 'absolute bottom-2 left-2 bg-yellow-600 bg-opacity-75 text-white px-2 py-1 rounded text-sm';
            
            // Show manual entry option as fallback
            document.getElementById('toggle-manual').click();
            return;
        }
        
        let bestMatch = null;
        let bestMatchDistance = Infinity;
        const MATCH_THRESHOLD = 0.6; // Lower value means stricter matching (0.6 is a good value)
        
        // Compare current face with all employees' stored face data
        for (const employee of employeesData) {
            try {
                // Skip if no face data available
                if (!employee.face_data) continue;
                
                // Parse employee face data
                const employeeFaceData = JSON.parse(employee.face_data);
                
                // Check if we have a proper descriptor or if we need to get it from raw data
                if (employeeFaceData.descriptor) {
                    // If we have a saved descriptor as Float32Array or array
                    const storedDescriptor = new Float32Array(employeeFaceData.descriptor);
                    
                    // Calculate Euclidean distance between face descriptors
                    // Lower distance means more similar faces
                    const distance = faceapi.euclideanDistance(faceDescriptor, storedDescriptor);
                    
                    console.log(`Distance for ${employee.name}: ${distance}`);
                    
                    // Update best match if this is closer
                    if (distance < bestMatchDistance) {
                        bestMatchDistance = distance;
                        bestMatch = employee;
                    }
                } 
                // For backward compatibility with older stored face data
                else if (employeeFaceData.detection) {
                    console.log(`Employee ${employee.name} has face data but no descriptor`);
                }
            } catch (error) {
                console.error(`Error comparing with employee ${employee.name}:`, error);
            }
        }
        
        // Check if the best match is good enough (below threshold)
        if (bestMatch && bestMatchDistance < MATCH_THRESHOLD) {
            console.log(`Recognized employee: ${bestMatch.name} with distance: ${bestMatchDistance}`);
            recognizedEmployee = bestMatch;
            
            // Display recognition result
            recognitionStatus.innerHTML = `
                <div class="flex items-center mb-2">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                        <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <div class="text-lg font-medium text-gray-900">Recognized: ${recognizedEmployee.name}</div>
                        <div class="text-sm text-gray-500">Employee ID: ${recognizedEmployee.employee_id}</div>
                        <div class="text-xs text-gray-400">Match confidence: ${((1 - bestMatchDistance) * 100).toFixed(1)}%</div>
                    </div>
                </div>
            `;
            
            recognitionStatus.classList.remove('hidden');
            recognitionStatus.classList.add('bg-blue-50', 'border', 'border-blue-200');
            
            // Enable attendance buttons
            clockInBtn.disabled = false;
            clockOutBtn.disabled = false;
        } else {
            console.log(`No matching employee found. Best distance: ${bestMatchDistance}`);
            recognizedEmployee = null;
            
            // Show not recognized message
            recognitionStatus.innerHTML = `
                <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                Face not recognized. Please try again or use the manual entry option below.
                            </p>
                        </div>
                    </div>
                </div>
            `;
            
            recognitionStatus.classList.remove('hidden');
            clockInBtn.disabled = true;
            clockOutBtn.disabled = true;
            
            // Show manual entry as fallback
            document.getElementById('toggle-manual').click();
        }
    }
    
    // Capture photo and submit attendance
    function captureAndSubmit(type) {
        if (!recognizedEmployee) return;
        
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
        
        // Use base64 image instead of File API
        const base64Image = canvas.toDataURL('image/jpeg');
        
        if (type === 'clock-in') {
            document.getElementById('face-clock-in-employee-id').value = recognizedEmployee.employee_id;
            document.getElementById('face-clock-in-photo').value = base64Image;
            document.getElementById('face-clock-in-form').submit();
        } else {
            document.getElementById('face-clock-out-employee-id').value = recognizedEmployee.employee_id;
            document.getElementById('face-clock-out-photo').value = base64Image;
            document.getElementById('face-clock-out-form').submit();
        }
    }
    
    // Handle clock in/out button clicks
    clockInBtn.addEventListener('click', () => captureAndSubmit('clock-in'));
    clockOutBtn.addEventListener('click', () => captureAndSubmit('clock-out'));
    
    // Toggle manual entry form
    document.getElementById('toggle-manual').addEventListener('click', function() {
        const form = document.getElementById('manual-form');
        if (form.classList.contains('hidden')) {
            form.classList.remove('hidden');
            this.textContent = 'Hide Manual Entry';
        } else {
            form.classList.add('hidden');
            this.textContent = 'Show Manual Entry';
        }
    });
    
    // Initialize face recognition when page loads
    window.addEventListener('DOMContentLoaded', initFaceRecognition);
    
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