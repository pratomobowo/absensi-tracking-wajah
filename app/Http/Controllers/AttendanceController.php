<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    /**
     * Display the attendance page
     */
    public function index()
    {
        return view('attendance.index');
    }

    /**
     * Validate employee by ID
     */
    public function validateEmployee(Request $request)
    {
        $request->validate([
            'employee_id' => 'required',
        ]);

        $employee = Employee::where('employee_id', $request->employee_id)
            ->where('is_active', true)
            ->first();

        if (!$employee) {
            return redirect()->back()->with('error', 'Employee not found or inactive.');
        }

        return view('attendance.attendance_form', compact('employee'));
    }

    /**
     * Clock in for attendance
     */
    public function clockIn(Request $request)
    {
        $request->validate([
            'employee_id' => 'required',
            'photo' => 'required',
        ]);

        $employee = Employee::where('employee_id', $request->employee_id)->firstOrFail();
        
        // Check if already clocked in today
        $existingAttendance = $employee->todayAttendance();
        if ($existingAttendance && $existingAttendance->clock_in) {
            return redirect()->route('attendance.index')->with('error', 'You have already clocked in today.');
        }

        // Process and save photo (base64)
        $photoPath = null;
        if ($request->has('photo') && strpos($request->photo, 'data:image') === 0) {
            $image = $request->photo;
            $image = str_replace('data:image/jpeg;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = 'clock_in_' . $employee->employee_id . '_' . time() . '.jpg';
            
            Storage::disk('public')->put('attendance_photos/' . $imageName, base64_decode($image));
            $photoPath = 'attendance_photos/' . $imageName;
        }

        // Create or update attendance record
        $attendance = $existingAttendance ?? new Attendance();
        $attendance->employee_id = $employee->id;
        $attendance->date = Carbon::today();
        $attendance->clock_in = Carbon::now();
        $attendance->clock_in_photo = $photoPath;
        $attendance->status = 'present';
        $attendance->ip_address = $request->ip();
        $attendance->device = $request->userAgent();
        $attendance->save();

        return redirect()->route('attendance.success', ['type' => 'clock-in', 'employee' => $employee->id]);
    }

    /**
     * Clock out for attendance
     */
    public function clockOut(Request $request)
    {
        $request->validate([
            'employee_id' => 'required',
            'photo' => 'required',
        ]);

        $employee = Employee::where('employee_id', $request->employee_id)->firstOrFail();
        
        // Check if already clocked in today
        $attendance = $employee->todayAttendance();
        if (!$attendance || !$attendance->clock_in) {
            return redirect()->route('attendance.index')->with('error', 'You have not clocked in today.');
        }

        if ($attendance->clock_out) {
            return redirect()->route('attendance.index')->with('error', 'You have already clocked out today.');
        }

        // Process and save photo (base64)
        $photoPath = null;
        if ($request->has('photo') && strpos($request->photo, 'data:image') === 0) {
            $image = $request->photo;
            $image = str_replace('data:image/jpeg;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = 'clock_out_' . $employee->employee_id . '_' . time() . '.jpg';
            
            Storage::disk('public')->put('attendance_photos/' . $imageName, base64_decode($image));
            $photoPath = 'attendance_photos/' . $imageName;
        }

        // Update attendance record
        $attendance->clock_out = Carbon::now();
        $attendance->clock_out_photo = $photoPath;
        $attendance->save();

        return redirect()->route('attendance.success', ['type' => 'clock-out', 'employee' => $employee->id]);
    }

    /**
     * Show success page after clock in/out
     */
    public function success($type, $employee)
    {
        $employee = Employee::findOrFail($employee);
        $attendance = $employee->todayAttendance();

        return view('attendance.success', compact('type', 'employee', 'attendance'));
    }
}
