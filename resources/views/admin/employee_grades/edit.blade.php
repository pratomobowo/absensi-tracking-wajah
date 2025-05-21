@extends('layouts.admin')

@section('title', 'Edit Employee Grade')

@section('header-title', 'Employee Grade Management')

@section('content')
<div class="mx-auto max-w-4xl">
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Edit Employee Grade</h2>
            <a href="{{ route('admin.employee-grades.index') }}" class="px-4 py-2 bg-gray-200 rounded-md text-gray-700 text-sm hover:bg-gray-300">
                Back to List
            </a>
        </div>
        
        <form action="{{ route('admin.employee-grades.update', $employeeGrade) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Grade Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $employeeGrade->name) }}" 
                            class="w-full border-gray-300 rounded-md shadow-sm p-2 focus:border-blue-500 focus:ring-blue-500" required>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Code</label>
                        <input type="text" id="code" name="code" value="{{ old('code', $employeeGrade->code) }}" 
                            class="w-full border-gray-300 rounded-md shadow-sm p-2 focus:border-blue-500 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Optional. Short code for this grade (e.g., E1, M3)</p>
                        @error('code')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea id="description" name="description" rows="3"
                            class="w-full border-gray-300 rounded-md shadow-sm p-2 focus:border-blue-500 focus:ring-blue-500">{{ old('description', $employeeGrade->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div>
                    <div class="mb-4">
                        <label for="level" class="block text-sm font-medium text-gray-700 mb-1">Level</label>
                        <input type="number" id="level" name="level" value="{{ old('level', $employeeGrade->level) }}" min="1" 
                            class="w-full border-gray-300 rounded-md shadow-sm p-2 focus:border-blue-500 focus:ring-blue-500" required>
                        <p class="text-xs text-gray-500 mt-1">Higher number means higher seniority</p>
                        @error('level')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="salary_min" class="block text-sm font-medium text-gray-700 mb-1">Minimum Salary</label>
                        <input type="number" id="salary_min" name="salary_min" value="{{ old('salary_min', $employeeGrade->salary_min) }}" min="0" step="0.01" 
                            class="w-full border-gray-300 rounded-md shadow-sm p-2 focus:border-blue-500 focus:ring-blue-500">
                        @error('salary_min')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="salary_max" class="block text-sm font-medium text-gray-700 mb-1">Maximum Salary</label>
                        <input type="number" id="salary_max" name="salary_max" value="{{ old('salary_max', $employeeGrade->salary_max) }}" min="0" step="0.01" 
                            class="w-full border-gray-300 rounded-md shadow-sm p-2 focus:border-blue-500 focus:ring-blue-500">
                        @error('salary_max')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $employeeGrade->is_active) ? 'checked' : '' }} 
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Active</span>
                        </label>
                    </div>
                    
                    <div class="mt-4 bg-gray-50 p-4 rounded-md border border-gray-200">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 text-gray-400">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-gray-600">
                                    This grade is currently assigned to <strong>{{ $employeeGrade->employees_count }}</strong> employee(s).
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 border-t border-gray-200 pt-6 flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Update Grade
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 