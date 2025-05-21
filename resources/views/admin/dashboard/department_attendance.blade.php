@extends('layouts.app')

@section('title', 'Department Attendance Dashboard')

@section('header-title', 'Department Attendance Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Department Info -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex flex-col md:flex-row md:items-center">
            <div class="flex-1">
                <h2 class="text-2xl font-bold text-gray-900">{{ $department->name }}</h2>
                <p class="mt-1 text-gray-600">{{ $department->description ?: 'No description available' }}</p>
                <div class="mt-2">
                    <span class="text-gray-500">Total Active Employees:</span>
                    <span class="ml-2 text-gray-900 font-medium">{{ $employees->count() }}</span>
                </div>
            </div>
            <div class="mt-4 md:mt-0 md:ml-6 flex flex-col items-center">
                <a href="{{ route('admin.dashboard.attendance') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>
    
    <!-- Date Range Filter -->
    <div class="bg-white rounded-lg shadow-md p-4">
        <form action="{{ route('admin.dashboard.department', ['department' => $department->id]) }}" method="GET" class="flex flex-wrap items-end gap-4">
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
    
    <!-- Department Attendance Summary -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-4 bg-purple-600 text-white">
            <h2 class="text-xl font-semibold">Department Attendance Summary</h2>
            <p class="text-sm">{{ $startDate->format('M d') }} - {{ $endDate->format('M d, Y') }} ({{ $workingDaysCount }} working days)</p>
        </div>
        <div class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <div class="text-sm font-medium text-blue-600">Overall Attendance Rate</div>
                    <div class="mt-1 text-2xl font-bold text-blue-700">
                        {{ $totalExpected > 0 ? round(($totalActual / $totalExpected) * 100, 1) : 0 }}%
                    </div>
                    <div class="text-sm text-blue-600">
                        {{ $totalActual }} / {{ $totalExpected }} total check-ins
                    </div>
                </div>
                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                    <div class="text-sm font-medium text-green-600">Present Employees (Avg.)</div>
                    <div class="mt-1 text-2xl font-bold text-green-700">
                        {{ $employees->count() > 0 && $workingDaysCount > 0 ? 
                            round($totalActual / $workingDaysCount, 1) : 0 }}
                    </div>
                    <div class="text-sm text-green-600">
                        out of {{ $employees->count() }} employees per day
                    </div>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                    <div class="text-sm font-medium text-yellow-600">Late Arrivals</div>
                    <div class="mt-1 text-2xl font-bold text-yellow-700">{{ $lateCount }}</div>
                    <div class="text-sm text-yellow-600">
                        {{ $totalActual > 0 ? round(($lateCount / $totalActual) * 100, 1) : 0 }}% of check-ins
                    </div>
                </div>
                <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                    <div class="text-sm font-medium text-red-600">Absent Employees (Avg.)</div>
                    <div class="mt-1 text-2xl font-bold text-red-700">
                        {{ $employees->count() > 0 && $workingDaysCount > 0 ? 
                            round(($totalExpected - $totalActual) / $workingDaysCount, 1) : 0 }}
                    </div>
                    <div class="text-sm text-red-600">
                        out of {{ $employees->count() }} employees per day
                    </div>
                </div>
            </div>
            
            <!-- Daily Attendance Trend Chart -->
            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Daily Attendance Trend</h3>
                <div class="relative h-80">
                    <canvas id="daily-trend-chart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Employee Attendance Comparison -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-4 bg-indigo-600 text-white">
            <h2 class="text-xl font-semibold">Employee Attendance Comparison</h2>
            <p class="text-sm">{{ $startDate->format('M d') }} - {{ $endDate->format('M d, Y') }}</p>
        </div>
        <div class="p-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Employee
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Present
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Absent
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Late
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Attendance Rate
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($employeeStats as $stat)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @if($stat['employee']->photo)
                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ Storage::url($stat['employee']->photo) }}" alt="{{ $stat['employee']->name }}">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                <span class="text-gray-500 text-sm">{{ strtoupper(substr($stat['employee']->name, 0, 1)) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $stat['employee']->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $stat['employee']->employee_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $stat['present_count'] }}
                                <span class="text-gray-500">({{ $workingDaysCount > 0 ? round(($stat['present_count'] / $workingDaysCount) * 100, 1) : 0 }}%)</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $stat['absent_count'] }}
                                <span class="text-gray-500">({{ $workingDaysCount > 0 ? round(($stat['absent_count'] / $workingDaysCount) * 100, 1) : 0 }}%)</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $stat['late_count'] }}
                                <span class="text-gray-500">({{ $stat['present_count'] > 0 ? round(($stat['late_count'] / $stat['present_count']) * 100, 1) : 0 }}%)</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ min($stat['attendance_rate'], 100) }}%"></div>
                                    </div>
                                    <span class="ml-2 text-sm text-gray-600">{{ $stat['attendance_rate'] }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <a href="{{ route('admin.dashboard.employee', ['employee' => $stat['employee']->id]) }}" class="text-blue-600 hover:text-blue-900">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Top Performers -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Punctual Employees -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-4 bg-green-600 text-white">
                <h2 class="text-xl font-semibold">Top Punctual Employees</h2>
                <p class="text-sm">Employees with highest attendance rates</p>
            </div>
            <div class="p-4">
                @php
                    $topPunctual = array_slice($employeeStats, 0, min(5, count($employeeStats)));
                @endphp
                
                <div class="space-y-4">
                    @foreach($topPunctual as $index => $stat)
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                @if($stat['employee']->photo)
                                    <img class="h-10 w-10 rounded-full object-cover" src="{{ Storage::url($stat['employee']->photo) }}" alt="{{ $stat['employee']->name }}">
                                @else
                                    <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-500 text-sm">{{ strtoupper(substr($stat['employee']->name, 0, 1)) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4 flex-1">
                                <div class="flex justify-between items-center">
                                    <div class="text-sm font-medium text-gray-900">{{ $stat['employee']->name }}</div>
                                    <div class="text-sm font-medium text-gray-900">{{ $stat['attendance_rate'] }}%</div>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2">
                                    <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ min($stat['attendance_rate'], 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- Needs Improvement -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-4 bg-yellow-600 text-white">
                <h2 class="text-xl font-semibold">Needs Improvement</h2>
                <p class="text-sm">Employees with lower attendance rates</p>
            </div>
            <div class="p-4">
                @php
                    $needsImprovement = array_slice(array_reverse($employeeStats), 0, min(5, count($employeeStats)));
                @endphp
                
                <div class="space-y-4">
                    @foreach($needsImprovement as $index => $stat)
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                @if($stat['employee']->photo)
                                    <img class="h-10 w-10 rounded-full object-cover" src="{{ Storage::url($stat['employee']->photo) }}" alt="{{ $stat['employee']->name }}">
                                @else
                                    <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-500 text-sm">{{ strtoupper(substr($stat['employee']->name, 0, 1)) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4 flex-1">
                                <div class="flex justify-between items-center">
                                    <div class="text-sm font-medium text-gray-900">{{ $stat['employee']->name }}</div>
                                    <div class="text-sm font-medium text-gray-900">{{ $stat['attendance_rate'] }}%</div>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2">
                                    <div class="bg-red-600 h-2.5 rounded-full" style="width: {{ min($stat['attendance_rate'], 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Daily Trend Chart
        const dailyTrendCtx = document.getElementById('daily-trend-chart').getContext('2d');
        
        new Chart(dailyTrendCtx, {
            type: 'line',
            data: {
                labels: {{ json_encode($dailyTrend['dates']) }},
                datasets: [
                    {
                        label: 'Attendance Rate (%)',
                        data: {{ json_encode($dailyTrend['attendance_rates']) }},
                        backgroundColor: 'rgba(59, 130, 246, 0.2)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 2,
                        yAxisID: 'y',
                        tension: 0.1
                    },
                    {
                        label: 'Present Employees',
                        data: {{ json_encode($dailyTrend['attendance_counts']) }},
                        backgroundColor: 'rgba(16, 185, 129, 0.2)',
                        borderColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 2,
                        yAxisID: 'y1',
                        tension: 0.1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Attendance Rate (%)'
                        },
                        min: 0,
                        max: 100
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Present Employees'
                        },
                        min: 0,
                        max: {{ $employees->count() }},
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                }
            }
        });
    });
</script>
@endsection 