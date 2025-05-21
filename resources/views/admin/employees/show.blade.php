@extends('layouts.admin')

@section('title', 'Employee Details')

@section('header-title', 'Employee Management')

@section('content')
<div class="mx-auto max-w-7xl">
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <div class="flex items-center">
                <h2 class="text-xl font-bold text-gray-800">Employee Profile</h2>
                @if($employee->status)
                    <span class="ml-3 px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full" 
                          style="background-color: {{ $employee->status->color }}20; color: {{ $employee->status->color }};">
                        {{ $employee->status->name }}
                    </span>
                @endif
                @if(!$employee->is_active)
                    <span class="ml-2 px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                        Inactive
                    </span>
                @endif
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('admin.employees.edit', $employee) }}" class="px-4 py-2 bg-blue-600 rounded-md text-white text-sm hover:bg-blue-700">
                    Edit
                </a>
                <a href="{{ route('admin.employees.index') }}" class="px-4 py-2 bg-gray-200 rounded-md text-gray-700 text-sm hover:bg-gray-300">
                    Back to List
                </a>
            </div>
        </div>
        
        <div class="p-6">
            <div class="flex flex-col md:flex-row">
                <div class="w-full md:w-1/3 mb-6 md:mb-0 md:pr-6">
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 text-center">
                        @if($employee->photo)
                            <img src="{{ Storage::url($employee->photo) }}" alt="{{ $employee->name }}" class="w-48 h-48 object-cover rounded-lg mx-auto mb-4">
                        @else
                            <div class="w-48 h-48 bg-gray-200 rounded-lg mx-auto mb-4 flex items-center justify-center">
                                <svg class="h-24 w-24 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                        @endif
                        
                        <h2 class="text-xl font-bold text-gray-800">{{ $employee->name }}</h2>
                        <p class="text-gray-600">{{ $employee->position }}</p>
                        
                        <div class="mt-4 flex flex-col space-y-2 text-sm">
                            <div class="flex items-center justify-center">
                                <svg class="h-5 w-5 text-gray-500 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                </svg>
                                <span>{{ $employee->email }}</span>
                            </div>
                            
                            <div class="flex items-center justify-center">
                                <svg class="h-5 w-5 text-gray-500 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                </svg>
                                <span>{{ $employee->phone }}</span>
                            </div>
                            
                            <div class="flex items-center justify-center">
                                <svg class="h-5 w-5 text-gray-500 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd" />
                                </svg>
                                <span>{{ $employee->department->name }}</span>
                            </div>
                            
                            @if($employee->grade)
                            <div class="flex items-center justify-center">
                                <svg class="h-5 w-5 text-gray-500 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5 4a3 3 0 00-3 3v6a3 3 0 003 3h10a3 3 0 003-3V7a3 3 0 00-3-3H5zm-1 9v-1h5v2H5a1 1 0 01-1-1zm7 1h4a1 1 0 001-1v-1h-5v2zm0-4h5V8h-5v2zM9 8H4v2h5V8z" clip-rule="evenodd" />
                                </svg>
                                <span>{{ $employee->grade->name }} {{ $employee->grade->code ? '('.$employee->grade->code.')' : '' }}</span>
                            </div>
                            @endif
                            
                            <div class="flex items-center justify-center">
                                <svg class="h-5 w-5 text-gray-500 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                </svg>
                                <span>Joined {{ $employee->joined_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                        
                        @if($employee->face_data)
                        <div class="mt-4 bg-blue-50 p-2 rounded text-sm text-blue-800 flex items-center justify-center">
                            <svg class="h-5 w-5 text-blue-500 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                            Face data registered
                        </div>
                        @endif
                    </div>
                </div>
                
                <div class="w-full md:w-2/3">
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8">
                            <button id="tab-personal" class="tab-button whitespace-nowrap py-4 px-1 border-b-2 border-blue-500 font-medium text-sm text-blue-600">
                                Personal Info
                            </button>
                            <button id="tab-employment" class="tab-button whitespace-nowrap py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                Employment Details
                            </button>
                            <button id="tab-attendance" class="tab-button whitespace-nowrap py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                Attendance History
                            </button>
                        </nav>
                    </div>
                    
                    <div id="content-personal" class="tab-content py-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if($employee->nik)
                            <div>
                                <h4 class="text-xs font-medium text-gray-500 uppercase">National ID</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ $employee->nik }}</p>
                            </div>
                            @endif
                            
                            @if($employee->birth_date)
                            <div>
                                <h4 class="text-xs font-medium text-gray-500 uppercase">Birth Date</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ $employee->birth_date->format('M d, Y') }} ({{ $employee->birth_date->age }} years)</p>
                            </div>
                            @endif
                            
                            @if($employee->gender)
                            <div>
                                <h4 class="text-xs font-medium text-gray-500 uppercase">Gender</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ ucfirst($employee->gender) }}</p>
                            </div>
                            @endif
                            
                            @if($employee->birth_place)
                            <div>
                                <h4 class="text-xs font-medium text-gray-500 uppercase">Birth Place</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ $employee->birth_place }}</p>
                            </div>
                            @endif
                            
                            @if($employee->address)
                            <div class="md:col-span-2">
                                <h4 class="text-xs font-medium text-gray-500 uppercase">Address</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ $employee->address }}</p>
                            </div>
                            @endif
                            
                            @if($employee->emergency_contact_name)
                            <div class="md:col-span-2 mt-4 border-t pt-4">
                                <h4 class="text-sm font-medium text-gray-700">Emergency Contact</h4>
                                <div class="mt-2 grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <h5 class="text-xs font-medium text-gray-500">Name</h5>
                                        <p class="mt-1 text-sm text-gray-900">{{ $employee->emergency_contact_name }}</p>
                                    </div>
                                    @if($employee->emergency_contact_phone)
                                    <div>
                                        <h5 class="text-xs font-medium text-gray-500">Phone</h5>
                                        <p class="mt-1 text-sm text-gray-900">{{ $employee->emergency_contact_phone }}</p>
                                    </div>
                                    @endif
                                    @if($employee->emergency_contact_relationship)
                                    <div>
                                        <h5 class="text-xs font-medium text-gray-500">Relationship</h5>
                                        <p class="mt-1 text-sm text-gray-900">{{ $employee->emergency_contact_relationship }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <div id="content-employment" class="tab-content py-4 hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h4 class="text-xs font-medium text-gray-500 uppercase">Employee ID</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ $employee->employee_id }}</p>
                            </div>
                            
                            <div>
                                <h4 class="text-xs font-medium text-gray-500 uppercase">Position</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ $employee->position }}</p>
                            </div>
                            
                            <div>
                                <h4 class="text-xs font-medium text-gray-500 uppercase">Department</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ $employee->department->name }}</p>
                            </div>
                            
                            @if($employee->status)
                            <div>
                                <h4 class="text-xs font-medium text-gray-500 uppercase">Employment Status</h4>
                                <p class="mt-1 text-sm">
                                    <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full" 
                                          style="background-color: {{ $employee->status->color }}20; color: {{ $employee->status->color }};">
                                        {{ $employee->status->name }}
                                    </span>
                                </p>
                            </div>
                            @endif
                            
                            @if($employee->grade)
                            <div>
                                <h4 class="text-xs font-medium text-gray-500 uppercase">Grade</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ $employee->grade->name }} {{ $employee->grade->code ? '('.$employee->grade->code.')' : '' }}</p>
                            </div>
                            @endif
                            
                            <div>
                                <h4 class="text-xs font-medium text-gray-500 uppercase">Joined Date</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ $employee->joined_at->format('M d, Y') }}</p>
                            </div>
                            
                            @if($employee->contract_start_date)
                            <div>
                                <h4 class="text-xs font-medium text-gray-500 uppercase">Contract Start</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ $employee->contract_start_date->format('M d, Y') }}</p>
                            </div>
                            @endif
                            
                            @if($employee->contract_end_date)
                            <div>
                                <h4 class="text-xs font-medium text-gray-500 uppercase">Contract End</h4>
                                <p class="mt-1 text-sm {{ $employee->isContractExpired() ? 'text-red-600' : ($employee->isContractNearExpiry() ? 'text-orange-600' : 'text-gray-900') }}">
                                    {{ $employee->contract_end_date->format('M d, Y') }}
                                    @if($employee->isContractExpired())
                                        <span class="text-xs text-red-600 font-medium">(Expired)</span>
                                    @elseif($employee->isContractNearExpiry())
                                        <span class="text-xs text-orange-600 font-medium">(Expires soon)</span>
                                    @endif
                                </p>
                            </div>
                            @endif
                            
                            @if($employee->salary)
                            <div>
                                <h4 class="text-xs font-medium text-gray-500 uppercase">Salary</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ number_format($employee->salary, 0) }}</p>
                            </div>
                            @endif
                            
                            @if($employee->bank_name && $employee->bank_account_number)
                            <div class="md:col-span-2">
                                <h4 class="text-xs font-medium text-gray-500 uppercase">Bank Account</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ $employee->bank_name }} - {{ $employee->bank_account_number }}</p>
                            </div>
                            @endif
                            
                            @if($employee->education_background)
                            <div class="md:col-span-2 mt-4 border-t pt-4">
                                <h4 class="text-sm font-medium text-gray-700">Education Background</h4>
                                <p class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ $employee->education_background }}</p>
                            </div>
                            @endif
                            
                            @if($employee->skills)
                            <div class="md:col-span-2 mt-4 border-t pt-4">
                                <h4 class="text-sm font-medium text-gray-700">Skills</h4>
                                <p class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ $employee->skills }}</p>
                            </div>
                            @endif
                            
                            @if($employee->notes)
                            <div class="md:col-span-2 mt-4 border-t pt-4">
                                <h4 class="text-sm font-medium text-gray-700">Notes</h4>
                                <p class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ $employee->notes }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <div id="content-attendance" class="tab-content py-4 hidden">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Recent Attendance</h3>
                            <a href="{{ route('admin.dashboard.employee', $employee) }}" class="text-sm text-blue-600 hover:text-blue-800">
                                View Full Attendance Dashboard →
                            </a>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clock In</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clock Out</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($employee->attendances as $attendance)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $attendance->date->format('D, M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $attendance->clock_in ? $attendance->clock_in->format('H:i:s') : '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $attendance->clock_out ? $attendance->clock_out->format('H:i:s') : '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($attendance->clock_in && $attendance->clock_out)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Complete
                                                    </span>
                                                @elseif($attendance->clock_in)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        Working
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        Absent
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                                No attendance records found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Tab functionality
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Remove active state from all tabs
            tabButtons.forEach(btn => {
                btn.classList.remove('border-blue-500', 'text-blue-600');
                btn.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Add active state to clicked tab
            button.classList.remove('border-transparent', 'text-gray-500');
            button.classList.add('border-blue-500', 'text-blue-600');
            
            // Hide all tab contents
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });
            
            // Show corresponding content
            const contentId = 'content-' + button.id.split('-')[1];
            document.getElementById(contentId).classList.remove('hidden');
        });
    });
</script>
@endpush
@endsection 