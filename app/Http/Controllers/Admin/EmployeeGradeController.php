<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeGrade;
use Illuminate\Http\Request;

class EmployeeGradeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $grades = EmployeeGrade::withCount('employees')->orderBy('level')->paginate(10);
        return view('admin.employee_grades.index', compact('grades'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.employee_grades.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:10|unique:employee_grades,code',
            'description' => 'nullable|string',
            'level' => 'required|integer|min:1',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gt:salary_min',
            'is_active' => 'boolean',
        ]);

        EmployeeGrade::create($request->all());

        return redirect()->route('admin.employee-grades.index')
            ->with('success', 'Employee grade created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(EmployeeGrade $employeeGrade)
    {
        $employeeGrade->load(['employees' => function($query) {
            $query->latest()->paginate(10);
        }]);
        
        return view('admin.employee_grades.show', compact('employeeGrade'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmployeeGrade $employeeGrade)
    {
        return view('admin.employee_grades.edit', compact('employeeGrade'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EmployeeGrade $employeeGrade)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:10|unique:employee_grades,code,' . $employeeGrade->id,
            'description' => 'nullable|string',
            'level' => 'required|integer|min:1',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gt:salary_min',
            'is_active' => 'boolean',
        ]);

        $employeeGrade->update($request->all());

        return redirect()->route('admin.employee-grades.index')
            ->with('success', 'Employee grade updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmployeeGrade $employeeGrade)
    {
        // Check if there are employees with this grade
        if ($employeeGrade->employees()->count() > 0) {
            return redirect()->route('admin.employee-grades.index')
                ->with('error', 'Cannot delete grade because it is assigned to employees.');
        }
        
        $employeeGrade->delete();

        return redirect()->route('admin.employee-grades.index')
            ->with('success', 'Employee grade deleted successfully.');
    }
}
