@extends('layouts.admin')

@section('title', 'Employee Management')

@section('header-title', 'Employee Management')

@section('content')
<div class="space-y-6">
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Employees</h2>
                <p class="text-sm text-gray-600">Manage all employees in your organization</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('admin.employees.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Add Employee
                </a>
                <button id="open-bulk-upload-btn" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    Bulk Upload Photos
                </button>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Employee
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Department
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Position
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Face Data
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($employees as $employee)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @if($employee->photo)
                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ Storage::url($employee->photo) }}" alt="{{ $employee->name }}">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                <span class="text-gray-500 text-sm">{{ strtoupper(substr($employee->name, 0, 1)) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $employee->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $employee->email }}</div>
                                        <div class="text-xs text-gray-500">ID: {{ $employee->employee_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $employee->department->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $employee->position }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($employee->is_active)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($employee->face_data)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Registered
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Not Registered
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('admin.employees.edit', $employee) }}" class="text-blue-600 hover:text-blue-900">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.employees.destroy', $employee) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this employee?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                No employees found. <a href="{{ route('admin.employees.create') }}" class="text-blue-600 hover:underline">Add your first employee</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 bg-gray-50">
            {{ $employees->links() }}
        </div>
    </div>
</div>

<!-- Bulk Upload Modal -->
<div id="bulk-upload-modal" class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-xl w-full mx-4">
        <div class="p-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Bulk Upload Employee Photos</h3>
                <button type="button" id="close-bulk-modal" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        
        <div class="p-6">
            <div class="text-sm text-gray-500 mb-4">
                <p>Upload multiple employee photos at once. The system will automatically attempt to detect faces and register face data for recognition.</p>
                <div class="mt-2 bg-yellow-50 border border-yellow-200 p-3 rounded-md">
                    <p><strong>Important notes:</strong></p>
                    <ul class="list-disc ml-4 mt-1">
                        <li>Each photo filename must follow format: <strong>employeeID_name.jpg</strong> (e.g., EMP001_john_doe.jpg)</li>
                        <li>Only JPG/JPEG/PNG files are allowed</li>
                        <li>Each photo should contain only one face clearly visible</li>
                        <li>Employees must already exist in the system with the correct employee ID</li>
                    </ul>
                </div>
            </div>
            
            <form id="bulk-upload-form" action="{{ route('admin.employees.bulk-upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="mt-2">
                    <label for="employee_photos" class="block text-sm font-medium text-gray-700 mb-1">Select Photos</label>
                    <input type="file" id="employee_photos" name="employee_photos[]" multiple accept=".jpg,.jpeg,.png" 
                        class="w-full border border-gray-300 rounded-md shadow-sm p-2 focus:border-blue-500 focus:ring-blue-500" required>
                </div>
                
                <div class="mt-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="update_existing" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Update existing face data (if already registered)</span>
                    </label>
                </div>
                
                <div class="flex justify-end space-x-3 mt-4">
                    <button type="button" id="cancel-bulk-upload" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Upload Photos
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Bulk upload modal elements
        const bulkUploadModal = document.getElementById('bulk-upload-modal');
        const openBulkUploadBtn = document.getElementById('open-bulk-upload-btn');
        const closeBulkModal = document.getElementById('close-bulk-modal');
        const cancelBulkUpload = document.getElementById('cancel-bulk-upload');
        
        // Event listeners for modal
        if (openBulkUploadBtn) {
            openBulkUploadBtn.addEventListener('click', function() {
                bulkUploadModal.classList.remove('hidden');
            });
        }
        
        if (closeBulkModal) {
            closeBulkModal.addEventListener('click', function() {
                bulkUploadModal.classList.add('hidden');
            });
        }
        
        if (cancelBulkUpload) {
            cancelBulkUpload.addEventListener('click', function() {
                bulkUploadModal.classList.add('hidden');
            });
        }
        
        // Preview selected images (optional)
        const employeePhotosInput = document.getElementById('employee_photos');
        
        if (employeePhotosInput) {
            employeePhotosInput.addEventListener('change', function() {
                const fileCount = this.files.length;
                const bulkUploadForm = document.getElementById('bulk-upload-form');
                
                if (fileCount > 0) {
                    const countInfo = document.createElement('div');
                    countInfo.className = 'mt-2 text-sm text-gray-600';
                    countInfo.textContent = `${fileCount} file(s) selected`;
                    
                    // Remove any previous count info
                    const existingInfo = bulkUploadForm.querySelector('.mt-2.text-sm.text-gray-600');
                    if (existingInfo) {
                        existingInfo.remove();
                    }
                    
                    // Insert after file input
                    employeePhotosInput.parentNode.appendChild(countInfo);
                }
            });
        }
    });
</script>
@endsection 