@extends('layouts.app')

@section('title', 'Edit Employee')

@section('header-title', 'Employee Management')

@section('content')
<div class="mx-auto max-w-4xl">
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Edit Employee</h2>
            <a href="{{ route('admin.employees.index') }}" class="px-4 py-2 bg-gray-200 rounded-md text-gray-700 text-sm hover:bg-gray-300">
                Back to List
            </a>
        </div>
        
        <form action="{{ route('admin.employees.update', $employee) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="mb-4">
                        <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-1">Employee ID</label>
                        <input type="text" id="employee_id" name="employee_id" value="{{ old('employee_id', $employee->employee_id) }}" 
                            class="w-full border-gray-300 rounded-md shadow-sm p-2 focus:border-blue-500 focus:ring-blue-500">
                        @error('employee_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $employee->name) }}" 
                            class="w-full border-gray-300 rounded-md shadow-sm p-2 focus:border-blue-500 focus:ring-blue-500">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $employee->email) }}" 
                            class="w-full border-gray-300 rounded-md shadow-sm p-2 focus:border-blue-500 focus:ring-blue-500">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $employee->phone) }}" 
                            class="w-full border-gray-300 rounded-md shadow-sm p-2 focus:border-blue-500 focus:ring-blue-500">
                        @error('phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="position" class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                        <input type="text" id="position" name="position" value="{{ old('position', $employee->position) }}" 
                            class="w-full border-gray-300 rounded-md shadow-sm p-2 focus:border-blue-500 focus:ring-blue-500">
                        @error('position')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="department_id" class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                        <select id="department_id" name="department_id" 
                            class="w-full border-gray-300 rounded-md shadow-sm p-2 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Department</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ old('department_id', $employee->department_id) == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="joined_at" class="block text-sm font-medium text-gray-700 mb-1">Joined Date</label>
                        <input type="date" id="joined_at" name="joined_at" value="{{ old('joined_at', $employee->joined_at ? $employee->joined_at->format('Y-m-d') : '') }}" 
                            class="w-full border-gray-300 rounded-md shadow-sm p-2 focus:border-blue-500 focus:ring-blue-500">
                        @error('joined_at')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $employee->is_active) ? 'checked' : '' }} 
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Active</span>
                        </label>
                    </div>
                </div>
                
                <div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Current Photo</label>
                        <div class="mt-1 relative">
                            @if($employee->photo)
                                <img src="{{ Storage::url($employee->photo) }}" alt="{{ $employee->name }}" class="w-32 h-32 object-cover rounded-lg border border-gray-300">
                            @else
                                <div class="w-32 h-32 bg-gray-100 flex items-center justify-center rounded-lg border border-gray-300">
                                    <span class="text-gray-500 text-sm">No photo</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="photo" class="block text-sm font-medium text-gray-700 mb-1">Upload New Photo</label>
                        <input type="file" id="photo" name="photo" accept="image/*" 
                            class="w-full border border-gray-300 rounded-md shadow-sm p-2 focus:border-blue-500 focus:ring-blue-500">
                        @error('photo')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mt-8 border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Face Recognition Data</h3>
                        
                        <div class="mb-4">
                            <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-blue-700">
                                            {{ $employee->face_data ? 'Face data already registered. You can update it using the capture button below.' : 'No face data registered yet. Use the capture button below to register face for recognition.' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <button type="button" id="open-face-capture" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V7a2 2 0 00-2-2h-1.586a1 1 0 01-.707-.293l-1.121-1.121A2 2 0 0011.172 3H8.828a2 2 0 00-1.414.586L6.293 4.707A1 1 0 015.586 5H4zm6 9a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                                </svg>
                                Capture Face Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 border-t border-gray-200 pt-6 flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Update Employee
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Face Capture Modal -->
<div id="face-capture-modal" class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-xl w-full mx-4">
        <div class="p-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Capture Face Data</h3>
                <button type="button" id="close-face-modal" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        
        <div class="p-6">
            <div class="text-sm text-gray-500 mb-4">
                Position your face in the center of the camera. Make sure your face is well-lit and look directly at the camera.
            </div>
            
            <div class="relative mb-6">
                <video id="face-video" class="w-full h-auto rounded-lg border border-gray-300" autoplay muted></video>
                <canvas id="face-overlay" class="absolute top-0 left-0 w-full h-full"></canvas>
                <div id="capture-status" class="absolute bottom-2 left-2 bg-gray-800 bg-opacity-75 text-white px-2 py-1 rounded text-sm">
                    Initializing camera...
                </div>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" id="cancel-capture" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cancel
                </button>
                <button type="button" id="capture-face" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" disabled>
                    Capture
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Face capture modal elements
        const faceModal = document.getElementById('face-capture-modal');
        const openFaceCapture = document.getElementById('open-face-capture');
        const closeFaceModal = document.getElementById('close-face-modal');
        const cancelCapture = document.getElementById('cancel-capture');
        const captureBtn = document.getElementById('capture-face');
        const video = document.getElementById('face-video');
        const overlay = document.getElementById('face-overlay');
        const captureStatus = document.getElementById('capture-status');
        
        let currentStream = null;
        let isModelLoaded = false;
        let faceDetectionInterval = null;
        
        // Event listeners for modal
        openFaceCapture.addEventListener('click', openFaceCaptureModal);
        closeFaceModal.addEventListener('click', closeFaceCaptureModal);
        cancelCapture.addEventListener('click', closeFaceCaptureModal);
        captureBtn.addEventListener('click', captureFace);
        
        // Initialize face detection
        async function initFaceDetection() {
            captureStatus.textContent = 'Loading face detection models...';
            
            try {
                // Use preloaded models if available
                if (window.faceApiInit && typeof window.faceApiInit === 'function') {
                    await window.faceApiInit();
                } else {
                    // Fallback to direct loading
                    await faceapi.loadSsdMobilenetv1Model('/models');
                    await faceapi.loadFaceLandmarkModel('/models');
                    await faceapi.loadFaceExpressionModel('/models');
                    await faceapi.loadFaceRecognitionNet('/models');
                }
                
                isModelLoaded = true;
                captureStatus.textContent = 'Models loaded. Starting camera...';
                
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ 
                        video: { 
                            facingMode: 'user',
                            width: { ideal: 640 },
                            height: { ideal: 480 }
                        }
                    });
                    
                    video.srcObject = stream;
                    currentStream = stream;
                    
                    video.onloadedmetadata = () => {
                        overlay.width = video.videoWidth;
                        overlay.height = video.videoHeight;
                    };
                    
                    video.addEventListener('play', startFaceDetection);
                    
                    captureStatus.textContent = 'Camera ready. Looking for face...';
                } catch (cameraError) {
                    captureStatus.textContent = `Camera error: ${cameraError.message}`;
                    captureStatus.className = 'absolute bottom-2 left-2 bg-red-600 bg-opacity-75 text-white px-2 py-1 rounded text-sm';
                }
            } catch (error) {
                console.error('Failed to load face detection models:', error);
                captureStatus.textContent = 'Failed to load face detection models';
                captureStatus.className = 'absolute bottom-2 left-2 bg-red-600 bg-opacity-75 text-white px-2 py-1 rounded text-sm';
            }
        }
        
        // Start face detection
        function startFaceDetection() {
            if (faceDetectionInterval) clearInterval(faceDetectionInterval);
            
            faceDetectionInterval = setInterval(async () => {
                if (!isModelLoaded || !video.videoWidth) return;
                
                try {
                    const detections = await faceapi.detectAllFaces(
                        video, 
                        new faceapi.SsdMobilenetv1Options({ minConfidence: 0.5 })
                    ).withFaceLandmarks();
                    
                    const ctx = overlay.getContext('2d');
                    ctx.clearRect(0, 0, overlay.width, overlay.height);
                    
                    if (detections.length > 0) {
                        // Draw detections
                        faceapi.draw.drawDetections(overlay, detections);
                        faceapi.draw.drawFaceLandmarks(overlay, detections);
                        
                        captureStatus.textContent = 'Face detected! Click capture button.';
                        captureStatus.className = 'absolute bottom-2 left-2 bg-green-600 bg-opacity-75 text-white px-2 py-1 rounded text-sm';
                        captureBtn.disabled = false;
                    } else {
                        captureStatus.textContent = 'No face detected';
                        captureStatus.className = 'absolute bottom-2 left-2 bg-gray-800 bg-opacity-75 text-white px-2 py-1 rounded text-sm';
                        captureBtn.disabled = true;
                    }
                } catch (error) {
                    captureStatus.textContent = 'Face detection error';
                    captureBtn.disabled = true;
                }
            }, 100);
        }
        
        // Open face capture modal
        function openFaceCaptureModal() {
            faceModal.classList.remove('hidden');
            initFaceDetection();
        }
        
        // Close face capture modal
        function closeFaceCaptureModal() {
            faceModal.classList.add('hidden');
            
            if (currentStream) {
                currentStream.getTracks().forEach(track => track.stop());
                currentStream = null;
            }
            
            if (faceDetectionInterval) {
                clearInterval(faceDetectionInterval);
                faceDetectionInterval = null;
            }
            
            captureBtn.disabled = true;
        }
        
        // Capture face
        async function captureFace() {
            if (!isModelLoaded) return;
            
            try {
                const detections = await faceapi.detectAllFaces(
                    video, 
                    new faceapi.SsdMobilenetv1Options({ minConfidence: 0.5 })
                )
                .withFaceLandmarks()
                .withFaceDescriptors();
                
                if (detections.length === 0) {
                    alert('No face detected. Please look directly at the camera.');
                    return;
                }
                
                // Take a photo
                const canvas = document.createElement('canvas');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                
                const faceImage = canvas.toDataURL('image/jpeg');
                
                // Get face descriptor for identification
                const faceDetection = detections[0];
                const simplifiedData = {
                    detection: {
                        box: faceDetection.detection.box,
                        score: faceDetection.detection.score
                    },
                    landmarks: {
                        positions: faceDetection.landmarks.positions.map(p => ({ x: p.x, y: p.y }))
                    },
                    descriptor: Array.from(faceDetection.descriptor)
                };
                
                captureStatus.textContent = 'Sending data to server...';
                
                // Send to server
                try {
                    const response = await fetch(`/admin/employees/{{ $employee->id }}/capture-face`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            face_photo: faceImage,
                            face_data: JSON.stringify(simplifiedData)
                        })
                    });
                    
                    if (!response.ok) {
                        // Try to parse error message if available
                        try {
                            const errorData = await response.json();
                            throw new Error(errorData.message || `Server responded with status: ${response.status}`);
                        } catch (jsonError) {
                            // If can't parse JSON, use response text or status
                            const errorText = await response.text();
                            throw new Error(errorText || `Server responded with status: ${response.status}`);
                        }
                    }
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        alert('Face data captured successfully!');
                        closeFaceCaptureModal();
                        
                        // If updating existing data, add note about attendance system
                        if ("{{ $employee->face_data }}" !== "") {
                            alert('Note: You have updated face data for this employee. If you previously had issues with face recognition, make sure to try the attendance system again with the new face data.');
                        }
                        
                        // Reload page to show updated data
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        alert('Failed to capture face data: ' + result.message);
                    }
                } catch (error) {
                    console.error('Error sending data to server:', error);
                    alert('Error capturing face: ' + error.message);
                }
            } catch (error) {
                console.error('Face detection error:', error);
                alert('Error during face detection: ' + error.message);
            }
        }
    });
</script>
@endsection 