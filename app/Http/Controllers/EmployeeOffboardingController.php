<?php

namespace App\Http\Controllers;

use App\Models\AssetAssignment;
use App\Models\Employee;

class EmployeeOffboardingController extends Controller
{
    public function show(Employee $employee)
    {
        $assignments = AssetAssignment::query()
            ->with(['asset.assetType'])
            ->where('employee_id', $employee->id)
            ->where('status', 'active')
            ->where('assignment_type', 'employee')
            ->orderBy('assigned_from', 'desc')
            ->get();

        return view('employees.offboarding', [
            'employee' => $employee,
            'assignments' => $assignments,
        ]);
    }
}