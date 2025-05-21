@extends('layouts.admin')

@section('title', 'Edit Department')

@section('header-title', 'Department Management')

@section('content')
<div class="mx-auto max-w-4xl">
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Edit Department</h2>
            <a href="{{ route('admin.departments.index') }}" class="px-4 py-2 bg-gray-200 rounded-md text-gray-700 text-sm hover:bg-gray-300">
                Back to List
            </a>
        </div>
        
        <form action="{{ route('admin.departments.update', $department) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Department Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $department->name) }}" 
                        class="w-full border-gray-300 rounded-md shadow-sm p-2 focus:border-blue-500 focus:ring-blue-500" required>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="description" name="description" rows="4" 
                        class="w-full border-gray-300 rounded-md shadow-sm p-2 focus:border-blue-500 focus:ring-blue-500">{{ old('description', $department->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Update Department
                </button>
            </div>
        </form>
    </div>
    
    <div class="mt-6 bg-white shadow-md rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Department Statistics</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <div class="text-sm font-medium text-gray-500">Total Employees</div>
                <div class="mt-1 text-2xl font-bold text-gray-900">{{ $department->employees->count() }}</div>
            </div>
            
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                <div class="text-sm font-medium text-blue-600">Active Employees</div>
                <div class="mt-1 text-2xl font-bold text-blue-700">
                    {{ $department->employees->where('is_active', true)->count() }}
                </div>
            </div>
            
            <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-200">
                <div class="text-sm font-medium text-indigo-600">Created</div>
                <div class="mt-1 text-lg font-bold text-indigo-700">
                    {{ $department->created_at->format('M d, Y') }}
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <a href="{{ route('admin.dashboard.department', $department) }}" class="text-blue-600 hover:text-blue-900 text-sm">
                View detailed attendance statistics →
            </a>
        </div>
    </div>
</div>
@endsection 