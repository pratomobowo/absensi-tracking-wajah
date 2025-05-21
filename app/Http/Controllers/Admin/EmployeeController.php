<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees
     */
    public function index()
    {
        $employees = Employee::with('department')->latest()->paginate(10);
        return view('admin.employees.index', compact('employees'));
    }
    
    /**
     * Show form to create a new employee
     */
    public function create()
    {
        $departments = Department::all();
        return view('admin.employees.create', compact('departments'));
    }
    
    /**
     * Store a newly created employee
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|unique:employees,employee_id',
            'name' => 'required',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'required',
            'position' => 'required',
            'department_id' => 'required|exists:departments,id',
            'photo' => 'nullable|image|max:2048',
            'joined_at' => 'required|date',
            'is_active' => 'boolean',
        ]);
        
        $data = $request->except('photo');
        
        // Handle photo upload
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('employee_photos', 'public');
            $data['photo'] = $path;
        }
        
        $employee = Employee::create($data);
        
        return redirect()->route('admin.employees.index')->with('success', 'Employee created successfully');
    }
    
    /**
     * Show employee details
     */
    public function show(Employee $employee)
    {
        return view('admin.employees.show', compact('employee'));
    }
    
    /**
     * Show form to edit employee
     */
    public function edit(Employee $employee)
    {
        $departments = Department::all();
        return view('admin.employees.edit', compact('employee', 'departments'));
    }
    
    /**
     * Update employee details
     */
    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'employee_id' => 'required|unique:employees,employee_id,' . $employee->id,
            'name' => 'required',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'phone' => 'required',
            'position' => 'required',
            'department_id' => 'required|exists:departments,id',
            'photo' => 'nullable|image|max:2048',
            'joined_at' => 'required|date',
            'is_active' => 'boolean',
        ]);
        
        $data = $request->except(['photo', '_token', '_method']);
        
        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($employee->photo) {
                Storage::disk('public')->delete($employee->photo);
            }
            
            $path = $request->file('photo')->store('employee_photos', 'public');
            $data['photo'] = $path;
        }
        
        $employee->update($data);
        
        return redirect()->route('admin.employees.index')->with('success', 'Employee updated successfully');
    }
    
    /**
     * Delete employee
     */
    public function destroy(Employee $employee)
    {
        // Delete employee photo if exists
        if ($employee->photo) {
            Storage::disk('public')->delete($employee->photo);
        }
        
        $employee->delete();
        
        return redirect()->route('admin.employees.index')->with('success', 'Employee deleted successfully');
    }
    
    /**
     * Capture face data for employee
     */
    public function captureFace(Request $request, Employee $employee)
    {
        try {
            $request->validate([
                'face_photo' => 'required|string',
                'face_data' => 'required|string',
            ]);
            
            // Store the base64 face photo
            if ($request->has('face_photo') && strpos($request->face_photo, 'data:image') === 0) {
                $image = $request->face_photo;
                $image = str_replace('data:image/jpeg;base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                $imageName = 'face_' . $employee->employee_id . '_' . time() . '.jpg';
                
                Storage::disk('public')->put('employee_faces/' . $imageName, base64_decode($image));
                
                // Jangan simpan semua data face detection, hanya simpan descriptor yang diperlukan
                $faceData = json_decode($request->face_data, true);
                
                // Hanya menyimpan informasi penting untuk mengurangi ukuran data
                $simplifiedData = [
                    'detection' => isset($faceData['detection']) ? $faceData['detection'] : $faceData,
                    'timestamp' => time()
                ];
                
                // Store face descriptor if available
                if (isset($faceData['descriptor'])) {
                    $simplifiedData['descriptor'] = $faceData['descriptor'];
                }
                
                // Update employee face data
                $employee->update([
                    'face_data' => json_encode($simplifiedData),
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Face data captured successfully',
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to capture face data: Invalid image format',
            ], 400);
        } catch (\Exception $e) {
            // Log error untuk debugging
            \Log::error('Face capture error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error processing face data: ' . $e->getMessage(),
            ], 500);
        }
    }
}
