@extends('layouts.app')

@section('title', 'Employee Attendance Dashboard')

@section('header-title', 'Employee Attendance Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Employee Info -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex flex-col md:flex-row md:items-center">
            <div class="flex-shrink-0 mb-4 md:mb-0 md:mr-6">
                @if($employee->photo)
                    <img class="h-24 w-24 rounded-full object-cover" src="{{ Storage::url($employee->photo) }}" alt="{{ $employee->name }}">
                @else
                    <div class="h-24 w-24 rounded-full bg-gray-200 flex items-center justify-center">
                        <span class="text-gray-500 text-xl">{{ strtoupper(substr($employee->name, 0, 1)) }}</span>
                    </div>
                @endif
            </div>
            <div class="flex-1">
                <h2 class="text-2xl font-bold text-gray-900">{{ $employee->name }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-2 gap-x-4 mt-2">
                    <div>
                        <span class="text-gray-500">Employee ID:</span>
                        <span class="ml-2 text-gray-900">{{ $employee->employee_id }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Position:</span>
                        <span class="ml-2 text-gray-900">{{ $employee->position }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Department:</span>
                        <span class="ml-2 text-gray-900">{{ $employee->department->name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Joined:</span>
                        <span class="ml-2 text-gray-900">{{ $employee->joined_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
            <div class="mt-4 md:mt-0 md:ml-6 flex flex-col items-center">
                <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back
                </a>
            </div>
        </div>
    </div>
    
    <!-- Date Range Filter -->
    <div class="bg-white rounded-lg shadow-md p-4">
        <form action="{{ route('admin.dashboard.employee', ['employee' => $employee->id]) }}" method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" id="start_date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" 
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input type="date" id="end_date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" 
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Apply Filter
                </button>
            </div>
        </form>
    </div>
    
    <!-- Attendance Summary -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-4 bg-blue-600 text-white">
            <h2 class="text-xl font-semibold">Attendance Summary</h2>
            <p class="text-sm">{{ $startDate->format('M d') }} - {{ $endDate->format('M d, Y') }}</p>
        </div>
        <div class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="text-sm font-medium text-gray-500">Working Days</div>
                    <div class="mt-1 text-2xl font-bold text-gray-900">{{ $workingDaysCount }}</div>
                </div>
                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                    <div class="text-sm font-medium text-green-600">Present</div>
                    <div class="mt-1 text-2xl font-bold text-green-700">{{ $presentDaysCount }}</div>
                    <div class="text-sm text-green-600">
                        {{ $workingDaysCount > 0 ? round(($presentDaysCount / $workingDaysCount) * 100, 1) : 0 }}% of working days
                    </div>
                </div>
                <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                    <div class="text-sm font-medium text-red-600">Absent</div>
                    <div class="mt-1 text-2xl font-bold text-red-700">{{ $absentDaysCount }}</div>
                    <div class="text-sm text-red-600">
                        {{ $workingDaysCount > 0 ? round(($absentDaysCount / $workingDaysCount) * 100, 1) : 0 }}% of working days
                    </div>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                    <div class="text-sm font-medium text-yellow-600">Late</div>
                    <div class="mt-1 text-2xl font-bold text-yellow-700">{{ $lateDaysCount }}</div>
                    <div class="text-sm text-yellow-600">
                        {{ $presentDaysCount > 0 ? round(($lateDaysCount / $presentDaysCount) * 100, 1) : 0 }}% of present days
                    </div>
                </div>
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <div class="text-sm font-medium text-blue-600">Avg. Work Hours</div>
                    <div class="mt-1 text-2xl font-bold text-blue-700">{{ $averageWorkHours }}</div>
                    <div class="text-sm text-blue-600">hours/day</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Clock In Trend -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-4 bg-indigo-600 text-white">
            <h2 class="text-xl font-semibold">Clock In Trend</h2>
            <p class="text-sm">Lower values indicate earlier arrival times</p>
        </div>
        <div class="p-4">
            <div class="relative h-80">
                <canvas id="clock-in-trend-chart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Attendance Records -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-4 bg-green-600 text-white">
            <h2 class="text-xl font-semibold">Attendance Records</h2>
            <p class="text-sm">{{ $attendanceRecords->count() }} records found</p>
        </div>
        <div class="p-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Clock In
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Clock Out
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Work Hours
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Notes
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($attendanceRecords as $record)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($record->date)->format('D, M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($record->clock_in)
                                    <div class="flex items-center">
                                        <span>{{ \Carbon\Carbon::parse($record->clock_in)->format('H:i:s') }}</span>
                                        @if($record->clock_in_photo)
                                            <a href="{{ Storage::url($record->clock_in_photo) }}" target="_blank" class="ml-2 text-blue-600 hover:text-blue-900">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($record->clock_out)
                                    <div class="flex items-center">
                                        <span>{{ \Carbon\Carbon::parse($record->clock_out)->format('H:i:s') }}</span>
                                        @if($record->clock_out_photo)
                                            <a href="{{ Storage::url($record->clock_out_photo) }}" target="_blank" class="ml-2 text-blue-600 hover:text-blue-900">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $record->clock_in && $record->clock_out ? $record->work_hours : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($record->status == 'present')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Present
                                    </span>
                                @elseif($record->status == 'late')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Late
                                    </span>
                                @elseif($record->status == 'absent')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Absent
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        {{ ucfirst($record->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $record->notes ?: '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                No attendance records found for this period.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Clock In Trend Chart
        const clockInTrendCtx = document.getElementById('clock-in-trend-chart').getContext('2d');
        
        new Chart(clockInTrendCtx, {
            type: 'line',
            data: {
                labels: {{ json_encode($attendanceTrend['dates']) }},
                datasets: [{
                    label: 'Clock In Time (hours)',
                    data: {{ json_encode($attendanceTrend['clock_in_times']) }},
                    backgroundColor: 'rgba(79, 70, 229, 0.2)',
                    borderColor: 'rgba(79, 70, 229, 1)',
                    borderWidth: 2,
                    tension: 0.1,
                    pointBackgroundColor: 'rgba(79, 70, 229, 1)',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        reverse: true, // Higher values (later times) at bottom
                        min: 6, // Start at 6 AM
                        max: 12, // End at 12 PM
                        title: {
                            display: true,
                            text: 'Clock In Time (24h format)'
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.parsed.y;
                                const hours = Math.floor(value);
                                const minutes = Math.round((value - hours) * 60);
                                return `Clock In: ${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection 