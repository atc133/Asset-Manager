<?php

namespace App\Http\Controllers;

use App\Models\AssetAssignment;
use App\Models\Employee;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ConsumableTransaction;

class EmployeeAssignmentFormController extends Controller
{
    public function pdf(Employee $employee)
    {
        return $this->checkout($employee);
    }

    public function checkout(Employee $employee)
    {
        $assignments = AssetAssignment::query()
            ->with([
                'asset.assetType',
                'asset.brand',
                'asset.assetModel',
                'asset.currentLocation',
            ])
            ->where('employee_id', $employee->id)
            ->where('status', 'active')
            ->where('assignment_type', 'employee')
            ->orderBy('assigned_from', 'desc')
            ->get();

$consumables = ConsumableTransaction::query()
    ->with('consumableType')
    ->where('type', 'stock_out')
    ->where('assignment_type', 'employee')
    ->where('employee_id', $employee->id)
    ->orderByDesc('created_at')
    ->get();

        return Pdf::loadView('exports.asset-checkout-form', [
    'employee' => $employee,
    'assignments' => $assignments,
    'consumables' => $consumables,
    'date' => now(),
    'documentTitle' => 'Asset Check-Out Form',
])
            ->setPaper('a4')
            ->download('asset_checkout_' . str($employee->full_name)->slug('_') . '.pdf');
    }

    public function checkin(Employee $employee)
    {
        $assignments = AssetAssignment::query()
            ->with([
                'asset.assetType',
                'asset.brand',
                'asset.assetModel',
                'asset.currentLocation',
            ])
            ->where('employee_id', $employee->id)
            ->where('assignment_type', 'employee')
            ->orderBy('assigned_from', 'desc')
            ->get();

$consumables = ConsumableTransaction::query()
    ->with('consumableType')
    ->where('type', 'stock_out')
    ->where('assignment_type', 'employee')
    ->where('employee_id', $employee->id)
    ->orderByDesc('created_at')
    ->get();
    return Pdf::loadView('exports.asset-checkin-form', [
    'employee' => $employee,
    'assignments' => $assignments,
    'consumables' => $consumables,
    'date' => now(),
    'documentTitle' => 'Asset Check-In Form',
])
            ->setPaper('a4')
            ->download('asset_checkin_' . str($employee->full_name)->slug('_') . '.pdf');
    }
}