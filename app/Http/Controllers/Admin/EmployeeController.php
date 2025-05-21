<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ProcessEmployeeFaceData;

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
    
    /**
     * Handle bulk upload of employee photos and register face data
     */
    public function bulkUpload(Request $request)
    {
        $request->validate([
            'employee_photos.*' => 'required|image|max:5120', // Max 5MB per image
            'update_existing' => 'nullable|boolean',
        ]);
        
        // Check if any files were uploaded
        if (!$request->hasFile('employee_photos')) {
            return redirect()->route('admin.employees.index')
                ->with('error', 'No files were uploaded.');
        }
        
        $updateExisting = $request->has('update_existing');
        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        
        // Load face-api models (outside the loop to avoid loading multiple times)
        
        // Process each uploaded photo
        foreach ($request->file('employee_photos') as $photo) {
            try {
                // Parse employee ID from filename (format: EMP001_name.jpg)
                $filenameWithoutExt = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                $parts = explode('_', $filenameWithoutExt);
                
                if (empty($parts[0])) {
                    $errors[] = "Invalid filename format for {$photo->getClientOriginalName()}. Expected format: employeeID_name.jpg";
                    $errorCount++;
                    continue;
                }
                
                $employeeId = $parts[0];
                
                // Find the employee by ID
                $employee = Employee::where('employee_id', $employeeId)->first();
                
                if (!$employee) {
                    $errors[] = "No employee found with ID: {$employeeId} for file {$photo->getClientOriginalName()}";
                    $errorCount++;
                    continue;
                }
                
                // Skip if employee already has face data and update_existing is false
                if ($employee->face_data && !$updateExisting) {
                    $errors[] = "Employee {$employee->name} already has face data. Skipped.";
                    $errorCount++;
                    continue;
                }
                
                // Store the photo
                $path = $photo->store('employee_faces', 'public');
                
                // Create a path that can be loaded by face-api.js
                $photoUrl = Storage::url($path);
                $photoPath = public_path(str_replace('/storage', 'storage', $photoUrl));
                
                // Process the image with face-api.js through a job
                ProcessEmployeeFaceData::dispatch($employee, $photoPath);
                
                $successCount++;
                
            } catch (\Exception $e) {
                \Log::error('Bulk upload error: ' . $e->getMessage());
                $errors[] = "Error processing {$photo->getClientOriginalName()}: {$e->getMessage()}";
                $errorCount++;
            }
        }
        
        // Prepare session message
        $sessionData = [
            'success_count' => $successCount,
            'error_count' => $errorCount,
            'errors' => $errors
        ];
        
        if ($successCount > 0) {
            return redirect()->route('admin.employees.index')
                ->with('bulk_upload_result', $sessionData)
                ->with('success', "{$successCount} photos uploaded successfully. {$errorCount} failed.");
        } else {
            return redirect()->route('admin.employees.index')
                ->with('bulk_upload_result', $sessionData)
                ->with('error', "Failed to process any photos. Please check the errors and try again.");
        }
    }
}
