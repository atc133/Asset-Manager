<?php

namespace App\Http\Controllers;

use App\Models\AssetAssignment;
use App\Models\Employee;
use Barryvdh\DomPDF\Facade\Pdf;

class EmployeeAssignmentFormController extends Controller
{
    public function pdf(Employee $employee)
    {
        $assignments = AssetAssignment::query()
            ->with([
                'asset.assetType',
                'asset.currentLocation',
            ])
            ->where('employee_id', $employee->id)
            ->where('status', 'active')
            ->where('assignment_type', 'employee')
            ->orderBy('assigned_from', 'desc')
            ->get();

        return Pdf::loadView('exports.employee-assignment-form', [
            'employee' => $employee,
            'assignments' => $assignments,
            'date' => now(),
        ])
            ->setPaper('a4')
            ->download('assignment_form_' . str($employee->full_name)->slug('_') . '.pdf');
    }
}