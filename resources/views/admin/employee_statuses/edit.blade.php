@extends('layouts.admin')

@section('title', 'Edit Employee Status')

@section('header-title', 'Employee Status Management')

@section('content')
<div class="mx-auto max-w-4xl">
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Edit Employee Status</h2>
            <a href="{{ route('admin.employee-statuses.index') }}" class="px-4 py-2 bg-gray-200 rounded-md text-gray-700 text-sm hover:bg-gray-300">
                Back to List
            </a>
        </div>
        
        <form action="{{ route('admin.employee-statuses.update', $employeeStatus) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Status Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $employeeStatus->name) }}" 
                        class="w-full border-gray-300 rounded-md shadow-sm p-2 focus:border-blue-500 focus:ring-blue-500" required>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="description" name="description" rows="3"
                        class="w-full border-gray-300 rounded-md shadow-sm p-2 focus:border-blue-500 focus:ring-blue-500">{{ old('description', $employeeStatus->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="color" class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                    <div class="flex space-x-3 items-center">
                        <input type="color" id="color" name="color" value="{{ old('color', $employeeStatus->color) }}" 
                            class="h-10 w-10 border-gray-300 rounded-md shadow-sm p-0">
                        <input type="text" id="color_hex" value="{{ old('color', $employeeStatus->color) }}"
                            class="w-32 border-gray-300 rounded-md shadow-sm p-2 focus:border-blue-500 focus:ring-blue-500" 
                            readonly>
                    </div>
                    @error('color')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $employeeStatus->is_active) ? 'checked' : '' }} 
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-md border border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 text-gray-400">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-gray-600">
                                This status is currently used by <strong>{{ $employeeStatus->employees_count }}</strong> employee(s).
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 border-t border-gray-200 pt-6 flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Update Status
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Update color hex text when color input changes
    document.getElementById('color').addEventListener('input', function(e) {
        document.getElementById('color_hex').value = e.target.value;
    });
</script>
@endpush
@endsection 