@extends('layouts.app')

@section('title', 'Employee Details')

@section('header-title', 'Employee Management')

@section('content')
<div class="mx-auto max-w-4xl">
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Employee Details</h2>
            <div class="flex space-x-3">
                <a href="{{ route('admin.employees.edit', $employee) }}" class="px-4 py-2 bg-blue-600 rounded-md text-white text-sm hover:bg-blue-700">
                    Edit Employee
                </a>
                <a href="{{ route('admin.employees.index') }}" class="px-4 py-2 bg-gray-200 rounded-md text-gray-700 text-sm hover:bg-gray-300">
                    Back to List
                </a>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Left Column - Photo and Basic Info -->
            <div class="md:col-span-1">
                <div class="flex flex-col items-center">
                    @if($employee->photo)
                        <img src="{{ Storage::url($employee->photo) }}" alt="{{ $employee->name }}" class="w-40 h-40 object-cover rounded-lg border border-gray-300">
                    @else
                        <div class="w-40 h-40 bg-gray-200 flex items-center justify-center rounded-lg border border-gray-300">
                            <span class="text-gray-500 text-4xl font-bold">{{ strtoupper(substr($employee->name, 0, 1)) }}</span>
                        </div>
                    @endif
                    
                    <div class="mt-4 text-center">
                        <h3 class="text-lg font-semibold text-gray-800">{{ $employee->name }}</h3>
                        <p class="text-sm text-gray-600">{{ $employee->position }}</p>
                        <div class="mt-2">
                            @if($employee->is_active)
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Inactive
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mt-6 w-full">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Face Recognition Status</h4>
                            <div class="flex items-center justify-center">
                                @if($employee->face_data)
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span class="ml-2 text-sm text-gray-800">Registered</span>
                                    </div>
                                @else
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        <span class="ml-2 text-sm text-gray-800">Not Registered</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Column - Details -->
            <div class="md:col-span-2">
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Employee Information</h3>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2">
                            <div>
                                <h4 class="text-xs font-medium text-gray-500">EMPLOYEE ID</h4>
                                <p class="text-sm text-gray-800">{{ $employee->employee_id }}</p>
                            </div>
                            <div>
                                <h4 class="text-xs font-medium text-gray-500">DEPARTMENT</h4>
                                <p class="text-sm text-gray-800">{{ $employee->department->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="text-xs font-medium text-gray-500">EMAIL</h4>
                            <p class="text-sm text-gray-800">{{ $employee->email }}</p>
                        </div>
                        
                        <div>
                            <h4 class="text-xs font-medium text-gray-500">PHONE</h4>
                            <p class="text-sm text-gray-800">{{ $employee->phone }}</p>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2">
                            <div>
                                <h4 class="text-xs font-medium text-gray-500">JOINED DATE</h4>
                                <p class="text-sm text-gray-800">{{ $employee->joined_at ? $employee->joined_at->format('d M, Y') : 'N/A' }}</p>
                            </div>
                            <div>
                                <h4 class="text-xs font-medium text-gray-500">EMPLOYMENT LENGTH</h4>
                                <p class="text-sm text-gray-800">{{ $employee->joined_at ? $employee->joined_at->diffForHumans(null, true) : 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Attendance</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clock In</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clock Out</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($employee->attendances()->latest()->take(5)->get() as $attendance)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                            {{ $attendance->date->format('d M, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                            {{ $attendance->clock_in ? date('H:i', strtotime($attendance->clock_in)) : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                            {{ $attendance->clock_out ? date('H:i', strtotime($attendance->clock_out)) : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($attendance->status === 'present')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Present
                                                </span>
                                            @elseif($attendance->status === 'late')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Late
                                                </span>
                                            @elseif($attendance->status === 'absent')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Absent
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    {{ ucfirst($attendance->status) }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                            No attendance records found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4 text-right">
                        <a href="#" class="text-sm text-blue-600 hover:text-blue-800">
                            View All Attendance Records →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 