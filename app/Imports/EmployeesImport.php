<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\Location;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EmployeesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['full_name'])) {
            return null;
        }

        $location = null;

        if (! empty($row['default_location_code'])) {
            $location = Location::where('code', $row['default_location_code'])->first();
        }

        return Employee::updateOrCreate(
            [
                'email' => $row['email'] ?? null,
            ],
            [
                'full_name' => $row['full_name'],
                'department' => $row['department'] ?? null,
                'work_mode' => $row['work_mode'] ?? 'office',
                'status' => $row['status'] ?? 'active',
                'default_location_id' => $location?->id,
            ]
        );
    }
}