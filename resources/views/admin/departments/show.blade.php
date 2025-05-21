@extends('layouts.admin')

@section('title', 'Department Details')

@section('header-title', 'Department Management')

@section('content')
<div class="mx-auto max-w-6xl">
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Department Details</h2>
            <div class="flex space-x-3">
                <a href="{{ route('admin.departments.edit', $department) }}" class="px-4 py-2 bg-blue-600 rounded-md text-white text-sm hover:bg-blue-700">
                    Edit Department
                </a>
                <a href="{{ route('admin.departments.index') }}" class="px-4 py-2 bg-gray-200 rounded-md text-gray-700 text-sm hover:bg-gray-300">
                    Back to List
                </a>
            </div>
        </div>
        
        <div class="bg-gray-50 rounded-lg p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Department Information</h3>
                    <div class="space-y-4">
                        <div>
                            <h4 class="text-xs font-medium text-gray-500">NAME</h4>
                            <p class="text-base text-gray-800">{{ $department->name }}</p>
                        </div>
                        <div>
                            <h4 class="text-xs font-medium text-gray-500">DESCRIPTION</h4>
                            <p class="text-base text-gray-800">{{ $department->description ?: 'No description available' }}</p>
                        </div>
                        <div>
                            <h4 class="text-xs font-medium text-gray-500">CREATED</h4>
                            <p class="text-base text-gray-800">{{ $department->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Statistics</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-white p-4 rounded-lg border border-gray-200">
                            <div class="text-sm font-medium text-gray-500">Total Employees</div>
                            <div class="mt-1 text-2xl font-bold text-gray-900">{{ $department->employees->count() }}</div>
                        </div>
                        
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <div class="text-sm font-medium text-blue-600">Active Employees</div>
                            <div class="mt-1 text-2xl font-bold text-blue-700">
                                {{ $department->employees->where('is_active', true)->count() }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('admin.dashboard.department', $department) }}" class="inline-flex items-center px-4 py-2 border border-blue-300 rounded-md shadow-sm text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="mr-2 h-5 w-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            View Attendance Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <h3 class="text-lg font-medium text-gray-900 mb-4">Employees in this Department</h3>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Employee
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Position
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Joined Date
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($department->employees as $employee)
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
                                        <div class="text-sm text-gray-500">{{ $employee->employee_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $employee->position }}
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $employee->joined_at ? $employee->joined_at->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.dashboard.employee', $employee) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                    View Stats
                                </a>
                                <a href="{{ route('admin.employees.edit', $employee) }}" class="text-blue-600 hover:text-blue-900">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                No employees in this department.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 