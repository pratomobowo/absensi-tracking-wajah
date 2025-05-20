<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\AttendanceReportController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Middleware\AdminAuthenticate;
use Illuminate\Support\Facades\Route;
use App\Models\Employee;

// Public routes
Route::get('/', function () {
    return redirect()->route('attendance.index');
});

// Attendance routes
Route::prefix('attendance')->name('attendance.')->group(function () {
    Route::get('/', [AttendanceController::class, 'index'])->name('index');
    Route::post('/validate-employee', [AttendanceController::class, 'validateEmployee'])->name('validate');
    Route::post('/clock-in', [AttendanceController::class, 'clockIn'])->name('clock-in');
    Route::post('/clock-out', [AttendanceController::class, 'clockOut'])->name('clock-out');
    Route::get('/success/{type}/{employee}', [AttendanceController::class, 'success'])->name('success');
});

// Face Recognition API - tambahkan di web routes juga untuk menghindari masalah CORS
Route::get('/api/employees-face-data', function() {
    try {
        // Ambil semua karyawan aktif dengan data wajah 
        $employees = Employee::where('is_active', true)
            ->whereNotNull('face_data')
            ->get(['id', 'employee_id', 'name', 'face_data', 'photo']);
        
        // Jika tidak ada karyawan di database, berikan data dummy agar tidak error
        if ($employees->isEmpty()) {
            return [
                [
                    'id' => 1,
                    'employee_id' => 'EMP001',
                    'name' => 'John Doe',
                    'face_data' => '', // Data wajah kosong
                    'photo' => null
                ]
            ];
        }
        
        return $employees;
    } catch (\Exception $e) {
        \Log::error('Error fetching employee face data: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to fetch employee data'], 500);
    }
});

// Admin auth routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest routes
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminLoginController::class, 'login'])->name('login.post');
    });

    // Protected routes
    Route::middleware([AdminAuthenticate::class])->group(function () {
        Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');
        
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Employee management
        Route::resource('employees', EmployeeController::class);
        // Capture face data endpoint
        Route::post('/employees/{employee}/capture-face', [EmployeeController::class, 'captureFace'])->name('employees.capture-face');
        
        // Department management
        Route::resource('departments', DepartmentController::class);
        
        // Attendance reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/daily', [AttendanceReportController::class, 'daily'])->name('daily');
            Route::get('/monthly', [AttendanceReportController::class, 'monthly'])->name('monthly');
            Route::get('/employee/{employee}', [AttendanceReportController::class, 'employee'])->name('employee');
            Route::get('/department/{department}', [AttendanceReportController::class, 'department'])->name('department');
        });
    });
});
