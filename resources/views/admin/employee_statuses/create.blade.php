@extends('layouts.admin')

@section('title', 'Add Employee Status')

@section('header-title', 'Employee Status Management')

@section('content')
<div class="mx-auto max-w-4xl">
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Add New Employee Status</h2>
            <a href="{{ route('admin.employee-statuses.index') }}" class="px-4 py-2 bg-gray-200 rounded-md text-gray-700 text-sm hover:bg-gray-300">
                Back to List
            </a>
        </div>
        
        <form action="{{ route('admin.employee-statuses.store') }}" method="POST">
            @csrf
            
            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Status Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" 
                        class="w-full border-gray-300 rounded-md shadow-sm p-2 focus:border-blue-500 focus:ring-blue-500" required>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="description" name="description" rows="3"
                        class="w-full border-gray-300 rounded-md shadow-sm p-2 focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="color" class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                    <div class="flex space-x-3 items-center">
                        <input type="color" id="color" name="color" value="{{ old('color', '#3b82f6') }}" 
                            class="h-10 w-10 border-gray-300 rounded-md shadow-sm p-0">
                        <input type="text" id="color_hex" value="{{ old('color', '#3b82f6') }}"
                            class="w-32 border-gray-300 rounded-md shadow-sm p-2 focus:border-blue-500 focus:ring-blue-500" 
                            readonly>
                    </div>
                    @error('color')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }} 
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>
                </div>
            </div>
            
            <div class="mt-6 border-t border-gray-200 pt-6 flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Save Status
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