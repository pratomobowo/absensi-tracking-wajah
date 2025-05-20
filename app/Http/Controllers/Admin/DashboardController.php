<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard
     */
    public function index()
    {
        // Get counts
        $totalEmployees = Employee::count();
        $totalDepartments = Department::count();
        $todayDate = Carbon::today()->toDateString();
        
        // Get today's attendance stats
        $todayStats = [
            'present' => Attendance::where('date', $todayDate)->where('status', 'present')->count(),
            'absent' => Employee::where('is_active', true)->count() - Attendance::where('date', $todayDate)->count(),
            'late' => Attendance::where('date', $todayDate)->where('status', 'late')->count(),
        ];
        
        // Get attendance for the past week
        $lastWeek = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $lastWeek[] = [
                'date' => $date->format('D'),
                'present' => Attendance::whereDate('date', $date->toDateString())->count(),
            ];
        }
        
        // Get department wise employee count
        $departmentData = Department::withCount('employees')->get();
        
        return view('admin.dashboard.index', compact(
            'totalEmployees',
            'totalDepartments',
            'todayStats',
            'lastWeek',
            'departmentData'
        ));
    }
}
