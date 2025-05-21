<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeStatus;
use Illuminate\Http\Request;

class EmployeeStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $statuses = EmployeeStatus::withCount('employees')->latest()->paginate(10);
        return view('admin.employee_statuses.index', compact('statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.employee_statuses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:employee_statuses',
            'description' => 'nullable|string',
            'color' => 'required|string|max:7',
            'is_active' => 'boolean',
        ]);

        EmployeeStatus::create($request->all());

        return redirect()->route('admin.employee-statuses.index')
            ->with('success', 'Employee status created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(EmployeeStatus $employeeStatus)
    {
        $employeeStatus->load(['employees' => function($query) {
            $query->latest()->paginate(10);
        }]);
        
        return view('admin.employee_statuses.show', compact('employeeStatus'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmployeeStatus $employeeStatus)
    {
        return view('admin.employee_statuses.edit', compact('employeeStatus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EmployeeStatus $employeeStatus)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:employee_statuses,name,' . $employeeStatus->id,
            'description' => 'nullable|string',
            'color' => 'required|string|max:7',
            'is_active' => 'boolean',
        ]);

        $employeeStatus->update($request->all());

        return redirect()->route('admin.employee-statuses.index')
            ->with('success', 'Employee status updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmployeeStatus $employeeStatus)
    {
        // Check if there are employees with this status
        if ($employeeStatus->employees()->count() > 0) {
            return redirect()->route('admin.employee-statuses.index')
                ->with('error', 'Cannot delete status because it is assigned to employees.');
        }
        
        $employeeStatus->delete();

        return redirect()->route('admin.employee-statuses.index')
            ->with('success', 'Employee status deleted successfully.');
    }
}
