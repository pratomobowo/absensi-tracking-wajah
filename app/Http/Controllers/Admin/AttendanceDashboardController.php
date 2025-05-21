<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceDashboardController extends Controller
{
    /**
     * Display the main attendance dashboard
     */
    public function index(Request $request)
    {
        // Get selected date range or use default (current month)
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfDay();
        
        // Summary statistics
        $totalEmployees = Employee::where('is_active', true)->count();
        
        // Today's attendance
        $todayAttendance = $this->getTodayAttendance();
        
        // Period attendance summary
        $attendanceSummary = $this->getAttendanceSummary($startDate, $endDate);
        
        // Department statistics
        $departmentStats = $this->getDepartmentStats($startDate, $endDate);
        
        // Recent attendance records
        $recentAttendance = Attendance::with('employee')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        // Daily attendance chart data
        $dailyAttendanceData = $this->getDailyAttendanceChartData($startDate, $endDate);
        
        return view('admin.dashboard.attendance', compact(
            'totalEmployees', 
            'todayAttendance',
            'attendanceSummary',
            'departmentStats',
            'recentAttendance',
            'dailyAttendanceData',
            'startDate',
            'endDate'
        ));
    }
    
    /**
     * Get today's attendance statistics
     */
    private function getTodayAttendance()
    {
        $today = Carbon::today();
        $totalActive = Employee::where('is_active', true)->count();
        
        $present = Attendance::whereDate('date', $today)
            ->whereNotNull('clock_in')
            ->count();
            
        $absent = $totalActive - $present;
        
        $late = Attendance::whereDate('date', $today)
            ->where('status', 'late')
            ->count();
            
        $onTime = $present - $late;
        
        // Attendance by hour
        $attendanceByHour = Attendance::whereDate('date', $today)
            ->whereNotNull('clock_in')
            ->select(DB::raw('HOUR(clock_in) as hour'), DB::raw('count(*) as count'))
            ->groupBy(DB::raw('HOUR(clock_in)'))
            ->orderBy('hour')
            ->get()
            ->pluck('count', 'hour')
            ->toArray();
            
        return [
            'date' => $today->toDateString(),
            'total_active' => $totalActive,
            'present' => $present,
            'absent' => $absent,
            'on_time' => $onTime,
            'late' => $late,
            'present_percentage' => $totalActive > 0 ? round(($present / $totalActive) * 100, 1) : 0,
            'attendance_by_hour' => $attendanceByHour
        ];
    }
    
    /**
     * Get attendance summary for date range
     */
    private function getAttendanceSummary($startDate, $endDate)
    {
        $workingDaysCount = $this->getWorkingDaysCount($startDate, $endDate);
        $totalActive = Employee::where('is_active', true)->count();
        
        $totalExpectedAttendance = $workingDaysCount * $totalActive;
        
        $totalAttendance = Attendance::whereBetween('date', [$startDate, $endDate])
            ->whereNotNull('clock_in')
            ->count();
            
        $lateCount = Attendance::whereBetween('date', [$startDate, $endDate])
            ->where('status', 'late')
            ->count();
            
        $absentCount = $totalExpectedAttendance - $totalAttendance;
        $absentCount = max(0, $absentCount); // Ensure we don't get negative absences
        
        return [
            'period' => $startDate->format('M d') . ' - ' . $endDate->format('M d, Y'),
            'working_days' => $workingDaysCount,
            'total_expected' => $totalExpectedAttendance,
            'total_actual' => $totalAttendance,
            'attendance_rate' => $totalExpectedAttendance > 0 ? 
                round(($totalAttendance / $totalExpectedAttendance) * 100, 1) : 0,
            'late_count' => $lateCount,
            'absent_count' => $absentCount
        ];
    }
    
    /**
     * Get department-wise attendance statistics
     */
    private function getDepartmentStats($startDate, $endDate)
    {
        $departments = Department::with(['employees' => function ($query) {
            $query->where('is_active', true);
        }])->get();
        
        $stats = [];
        
        foreach ($departments as $department) {
            $employeeIds = $department->employees->pluck('id')->toArray();
            
            if (empty($employeeIds)) {
                continue;
            }
            
            $workingDaysCount = $this->getWorkingDaysCount($startDate, $endDate);
            $totalActive = count($employeeIds);
            
            $totalExpected = $workingDaysCount * $totalActive;
            
            $totalActual = Attendance::whereBetween('date', [$startDate, $endDate])
                ->whereIn('employee_id', $employeeIds)
                ->whereNotNull('clock_in')
                ->count();
                
            $lateCount = Attendance::whereBetween('date', [$startDate, $endDate])
                ->whereIn('employee_id', $employeeIds)
                ->where('status', 'late')
                ->count();
                
            $stats[] = [
                'department' => $department->name,
                'active_employees' => $totalActive,
                'attendance_rate' => $totalExpected > 0 ? 
                    round(($totalActual / $totalExpected) * 100, 1) : 0,
                'late_percentage' => $totalActual > 0 ? 
                    round(($lateCount / $totalActual) * 100, 1) : 0
            ];
        }
        
        return $stats;
    }
    
    /**
     * Get daily attendance chart data
     */
    private function getDailyAttendanceChartData($startDate, $endDate)
    {
        $attendanceData = Attendance::whereBetween('date', [$startDate, $endDate])
            ->select('date', DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date')
            ->map(function ($item) {
                return $item->count;
            })
            ->toArray();
            
        $lateData = Attendance::whereBetween('date', [$startDate, $endDate])
            ->where('status', 'late')
            ->select('date', DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date')
            ->map(function ($item) {
                return $item->count;
            })
            ->toArray();
        
        $dates = [];
        $present = [];
        $late = [];
        
        $currentDate = clone $startDate;
        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->toDateString();
            $dates[] = $currentDate->format('M d');
            $present[] = $attendanceData[$dateStr] ?? 0;
            $late[] = $lateData[$dateStr] ?? 0;
            
            $currentDate->addDay();
        }
        
        return [
            'dates' => $dates,
            'present' => $present,
            'late' => $late
        ];
    }
    
    /**
     * Get the number of working days in a date range (excluding weekends)
     */
    private function getWorkingDaysCount($startDate, $endDate)
    {
        $days = 0;
        $currentDate = clone $startDate;
        
        while ($currentDate <= $endDate) {
            // Exclude weekends (6 = Saturday, 0 = Sunday)
            $dayOfWeek = $currentDate->dayOfWeek;
            if ($dayOfWeek != 0 && $dayOfWeek != 6) {
                $days++;
            }
            
            $currentDate->addDay();
        }
        
        return $days;
    }
    
    /**
     * Display dashboard for a specific employee
     */
    public function employeeDashboard(Request $request, Employee $employee)
    {
        // Get selected date range or use default (current month)
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfDay();
        
        // Get all attendance records for the employee in date range
        $attendanceRecords = Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();
            
        // Calculate attendance statistics
        $workingDaysCount = $this->getWorkingDaysCount($startDate, $endDate);
        $presentDaysCount = $attendanceRecords->whereNotNull('clock_in')->count();
        $lateDaysCount = $attendanceRecords->where('status', 'late')->count();
        $absentDaysCount = $workingDaysCount - $presentDaysCount;
        $absentDaysCount = max(0, $absentDaysCount);
        
        // Calculate average work hours
        $totalWorkHours = 0;
        $recordsWithHours = 0;
        
        foreach ($attendanceRecords as $record) {
            if ($record->clock_in && $record->clock_out) {
                $totalWorkHours += $record->work_hours;
                $recordsWithHours++;
            }
        }
        
        $averageWorkHours = $recordsWithHours > 0 ? 
            round($totalWorkHours / $recordsWithHours, 1) : 0;
            
        // Get attendance trend for chart
        $attendanceTrend = $this->getEmployeeAttendanceTrend($employee->id, $startDate, $endDate);
        
        return view('admin.dashboard.employee_attendance', compact(
            'employee',
            'attendanceRecords',
            'startDate',
            'endDate',
            'workingDaysCount',
            'presentDaysCount',
            'lateDaysCount',
            'absentDaysCount',
            'averageWorkHours',
            'attendanceTrend'
        ));
    }
    
    /**
     * Get attendance trend data for a specific employee
     */
    private function getEmployeeAttendanceTrend($employeeId, $startDate, $endDate)
    {
        $attendanceData = Attendance::where('employee_id', $employeeId)
            ->whereBetween('date', [$startDate, $endDate])
            ->whereNotNull('clock_in')
            ->select('date', DB::raw('TIME_TO_SEC(clock_in) as clock_in_seconds'))
            ->orderBy('date')
            ->get();
            
        $dates = [];
        $clockInTimes = [];
        
        foreach ($attendanceData as $record) {
            $dates[] = Carbon::parse($record->date)->format('M d');
            // Convert seconds to hours for better visualization (e.g., 8.5 for 8:30 AM)
            $clockInTimes[] = round($record->clock_in_seconds / 3600, 2);
        }
        
        return [
            'dates' => $dates,
            'clock_in_times' => $clockInTimes
        ];
    }
    
    /**
     * Display dashboard for a specific department
     */
    public function departmentDashboard(Request $request, Department $department)
    {
        // Get selected date range or use default (current month)
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfDay();
        
        // Get employees in the department
        $employees = Employee::where('department_id', $department->id)
            ->where('is_active', true)
            ->get();
            
        $employeeIds = $employees->pluck('id')->toArray();
        
        // Skip if no employees found
        if (empty($employeeIds)) {
            return redirect()->route('admin.dashboard.attendance')
                ->with('error', 'No active employees found in this department.');
        }
        
        // Get attendance statistics
        $workingDaysCount = $this->getWorkingDaysCount($startDate, $endDate);
        $totalExpected = $workingDaysCount * count($employeeIds);
        
        $attendanceData = Attendance::whereBetween('date', [$startDate, $endDate])
            ->whereIn('employee_id', $employeeIds)
            ->get();
            
        $totalActual = $attendanceData->whereNotNull('clock_in')->count();
        $lateCount = $attendanceData->where('status', 'late')->count();
        
        // Employee-wise attendance statistics
        $employeeStats = [];
        
        foreach ($employees as $employee) {
            $employeeAttendance = $attendanceData->where('employee_id', $employee->id);
            $presentCount = $employeeAttendance->whereNotNull('clock_in')->count();
            $lateCount = $employeeAttendance->where('status', 'late')->count();
            
            $employeeStats[] = [
                'employee' => $employee,
                'present_count' => $presentCount,
                'absent_count' => $workingDaysCount - $presentCount,
                'late_count' => $lateCount,
                'attendance_rate' => $workingDaysCount > 0 ? 
                    round(($presentCount / $workingDaysCount) * 100, 1) : 0
            ];
        }
        
        // Sort by attendance rate (descending)
        usort($employeeStats, function ($a, $b) {
            return $b['attendance_rate'] <=> $a['attendance_rate'];
        });
        
        // Daily attendance trend
        $dailyTrend = $this->getDepartmentDailyTrend($department->id, $startDate, $endDate);
        
        return view('admin.dashboard.department_attendance', compact(
            'department',
            'employees',
            'startDate',
            'endDate',
            'workingDaysCount',
            'totalExpected',
            'totalActual',
            'lateCount',
            'employeeStats',
            'dailyTrend'
        ));
    }
    
    /**
     * Get daily attendance trend for a department
     */
    private function getDepartmentDailyTrend($departmentId, $startDate, $endDate)
    {
        $employeeIds = Employee::where('department_id', $departmentId)
            ->where('is_active', true)
            ->pluck('id')
            ->toArray();
            
        if (empty($employeeIds)) {
            return [
                'dates' => [],
                'attendance_counts' => [],
                'attendance_rates' => []
            ];
        }
        
        $dailyData = Attendance::whereBetween('date', [$startDate, $endDate])
            ->whereIn('employee_id', $employeeIds)
            ->whereNotNull('clock_in')
            ->select('date', DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date')
            ->map(function ($item) {
                return $item->count;
            })
            ->toArray();
            
        $dates = [];
        $counts = [];
        $rates = [];
        $employeeCount = count($employeeIds);
        
        $currentDate = clone $startDate;
        while ($currentDate <= $endDate) {
            // Skip weekends
            $dayOfWeek = $currentDate->dayOfWeek;
            if ($dayOfWeek != 0 && $dayOfWeek != 6) {
                $dateStr = $currentDate->toDateString();
                $dates[] = $currentDate->format('M d');
                $count = $dailyData[$dateStr] ?? 0;
                $counts[] = $count;
                $rates[] = $employeeCount > 0 ? round(($count / $employeeCount) * 100, 1) : 0;
            }
            
            $currentDate->addDay();
        }
        
        return [
            'dates' => $dates,
            'attendance_counts' => $counts,
            'attendance_rates' => $rates
        ];
    }
} 