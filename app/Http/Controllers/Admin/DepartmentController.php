<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of departments
     */
    public function index()
    {
        $departments = Department::withCount('employees')->latest()->paginate(10);
        return view('admin.departments.index', compact('departments'));
    }
    
    /**
     * Show form to create a new department
     */
    public function create()
    {
        return view('admin.departments.create');
    }
    
    /**
     * Store a newly created department
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:departments,name|max:255',
            'description' => 'nullable|max:1000',
        ]);
        
        $department = Department::create($request->all());
        
        return redirect()->route('admin.departments.index')->with('success', 'Department created successfully');
    }
    
    /**
     * Show department details
     */
    public function show(Department $department)
    {
        $department->load('employees');
        return view('admin.departments.show', compact('department'));
    }
    
    /**
     * Show form to edit department
     */
    public function edit(Department $department)
    {
        return view('admin.departments.edit', compact('department'));
    }
    
    /**
     * Update department details
     */
    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|max:255|unique:departments,name,' . $department->id,
            'description' => 'nullable|max:1000',
        ]);
        
        $department->update($request->all());
        
        return redirect()->route('admin.departments.index')->with('success', 'Department updated successfully');
    }
    
    /**
     * Delete department
     */
    public function destroy(Department $department)
    {
        // Check if department has employees
        if ($department->employees()->count() > 0) {
            return redirect()->route('admin.departments.index')->with('error', 'Cannot delete department with employees. Please reassign employees first.');
        }
        
        $department->delete();
        
        return redirect()->route('admin.departments.index')->with('success', 'Department deleted successfully');
    }
}
