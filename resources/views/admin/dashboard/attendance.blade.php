@extends('layouts.admin')

@section('title', 'Attendance Dashboard')

@section('header-title', 'Attendance Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Date Range Filter -->
    <div class="bg-white rounded-lg shadow-md p-4">
        <form action="{{ route('admin.dashboard.attendance') }}" method="GET" class="flex flex-wrap items-end gap-4">
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
            <div>
                <a href="{{ route('admin.dashboard.attendance') }}" class="inline-block py-2 px-4 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Reset
                </a>
            </div>
        </form>
    </div>
    
    <!-- Today's Summary -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-4 bg-blue-600 text-white">
            <h2 class="text-xl font-semibold">Today's Attendance Summary</h2>
            <p class="text-sm">{{ \Carbon\Carbon::parse($todayAttendance['date'])->format('l, F d, Y') }}</p>
        </div>
        <div class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="text-sm font-medium text-gray-500">Total Active Employees</div>
                    <div class="mt-1 text-2xl font-bold text-gray-900">{{ $todayAttendance['total_active'] }}</div>
                </div>
                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                    <div class="text-sm font-medium text-green-600">Present</div>
                    <div class="mt-1 text-2xl font-bold text-green-700">{{ $todayAttendance['present'] }}</div>
                    <div class="text-sm text-green-600">{{ $todayAttendance['present_percentage'] }}% of workforce</div>
                </div>
                <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                    <div class="text-sm font-medium text-red-600">Absent</div>
                    <div class="mt-1 text-2xl font-bold text-red-700">{{ $todayAttendance['absent'] }}</div>
                    <div class="text-sm text-red-600">{{ 100 - $todayAttendance['present_percentage'] }}% of workforce</div>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                    <div class="text-sm font-medium text-yellow-600">Late</div>
                    <div class="mt-1 text-2xl font-bold text-yellow-700">{{ $todayAttendance['late'] }}</div>
                    <div class="text-sm text-yellow-600">{{ $todayAttendance['present'] > 0 ? round(($todayAttendance['late'] / $todayAttendance['present']) * 100, 1) : 0 }}% of present</div>
                </div>
            </div>
            
            <!-- Attendance by Hour Chart -->
            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Today's Attendance by Hour</h3>
                <div class="relative h-60">
                    <canvas id="hourly-chart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Period Summary -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-4 bg-indigo-600 text-white">
            <h2 class="text-xl font-semibold">Attendance Summary</h2>
            <p class="text-sm">{{ $attendanceSummary['period'] }}</p>
        </div>
        <div class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="text-sm font-medium text-gray-500">Working Days</div>
                    <div class="mt-1 text-2xl font-bold text-gray-900">{{ $attendanceSummary['working_days'] }}</div>
                </div>
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <div class="text-sm font-medium text-blue-600">Attendance Rate</div>
                    <div class="mt-1 text-2xl font-bold text-blue-700">{{ $attendanceSummary['attendance_rate'] }}%</div>
                    <div class="text-sm text-blue-600">
                        {{ $attendanceSummary['total_actual'] }} / {{ $attendanceSummary['total_expected'] }} total check-ins
                    </div>
                </div>
                <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                    <div class="text-sm font-medium text-red-600">Absences</div>
                    <div class="mt-1 text-2xl font-bold text-red-700">{{ $attendanceSummary['absent_count'] }}</div>
                    <div class="text-sm text-red-600">
                        {{ $attendanceSummary['total_expected'] > 0 ? round(($attendanceSummary['absent_count'] / $attendanceSummary['total_expected']) * 100, 1) : 0 }}% of expected attendance
                    </div>
                </div>
            </div>
            
            <!-- Daily Attendance Chart -->
            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Daily Attendance</h3>
                <div class="relative h-80">
                    <canvas id="daily-chart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Department Statistics -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-4 bg-purple-600 text-white">
            <h2 class="text-xl font-semibold">Department Statistics</h2>
            <p class="text-sm">{{ $attendanceSummary['period'] }}</p>
        </div>
        <div class="p-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Department
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Active Employees
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Attendance Rate
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Late %
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($departmentStats as $stat)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $stat['department'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $stat['active_employees'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ min($stat['attendance_rate'], 100) }}%"></div>
                                    </div>
                                    <span class="ml-2 text-sm text-gray-600">{{ $stat['attendance_rate'] }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="bg-yellow-500 h-2.5 rounded-full" style="width: {{ min($stat['late_percentage'], 100) }}%"></div>
                                    </div>
                                    <span class="ml-2 text-sm text-gray-600">{{ $stat['late_percentage'] }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <a href="{{ route('admin.dashboard.department', ['department' => array_search($stat['department'], array_column($departmentStats, 'department')) + 1]) }}" class="text-blue-600 hover:text-blue-900">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Recent Attendance -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-4 bg-green-600 text-white">
            <h2 class="text-xl font-semibold">Recent Attendance Records</h2>
        </div>
        <div class="p-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Employee
                        </th>
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
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($recentAttendance as $attendance)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @if($attendance->employee->photo)
                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ Storage::url($attendance->employee->photo) }}" alt="{{ $attendance->employee->name }}">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                <span class="text-gray-500 text-sm">{{ strtoupper(substr($attendance->employee->name, 0, 1)) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $attendance->employee->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $attendance->employee->employee_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($attendance->date)->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i:s') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i:s') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($attendance->status == 'present')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Present
                                    </span>
                                @elseif($attendance->status == 'late')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Late
                                    </span>
                                @elseif($attendance->status == 'absent')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Absent
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        {{ ucfirst($attendance->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <a href="{{ route('admin.dashboard.employee', ['employee' => $attendance->employee->id]) }}" class="text-blue-600 hover:text-blue-900">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @endforeach
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
        // Hourly Attendance Chart
        const hourlyCtx = document.getElementById('hourly-chart').getContext('2d');
        
        // Prepare data
        const hours = Array.from({length: 24}, (_, i) => i);
        const hourlyData = hours.map(hour => {
            return {{ json_encode($todayAttendance['attendance_by_hour']) }}[hour] || 0;
        });
        
        const hourlyLabels = hours.map(hour => {
            return `${hour}:00`;
        });
        
        new Chart(hourlyCtx, {
            type: 'bar',
            data: {
                labels: hourlyLabels,
                datasets: [{
                    label: 'Check-ins',
                    data: hourlyData,
                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
        
        // Daily Attendance Chart
        const dailyCtx = document.getElementById('daily-chart').getContext('2d');
        
        new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: {{ json_encode($dailyAttendanceData['dates']) }},
                datasets: [
                    {
                        label: 'Present',
                        data: {{ json_encode($dailyAttendanceData['present']) }},
                        backgroundColor: 'rgba(16, 185, 129, 0.2)',
                        borderColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 2,
                        tension: 0.1
                    },
                    {
                        label: 'Late',
                        data: {{ json_encode($dailyAttendanceData['late']) }},
                        backgroundColor: 'rgba(245, 158, 11, 0.2)',
                        borderColor: 'rgba(245, 158, 11, 1)',
                        borderWidth: 2,
                        tension: 0.1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
@endsection 