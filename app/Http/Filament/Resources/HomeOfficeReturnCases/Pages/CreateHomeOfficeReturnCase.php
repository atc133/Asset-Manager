<?php

namespace App\Filament\Resources\HomeOfficeReturnCases\Pages;

use App\Filament\Resources\HomeOfficeReturnCases\HomeOfficeReturnCaseResource;
use App\Models\AssetAssignment;
use App\Models\HomeOfficeReturnItem;
use Filament\Resources\Pages\CreateRecord;

class CreateHomeOfficeReturnCase extends CreateRecord
{
    protected static string $resource = HomeOfficeReturnCaseResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['requested_by_user_id'] = auth()->id();
        $data['requested_at'] = now();

        return $data;
    }

    protected function afterCreate(): void
    {
        $employeeId = $this->record->employee_id;

        $activeAssignments = AssetAssignment::query()
            ->where('employee_id', $employeeId)
            ->where('assignment_type', 'employee')
            ->where('status', 'active')
            ->with('asset')
            ->get();

        foreach ($activeAssignments as $assignment) {
            HomeOfficeReturnItem::firstOrCreate([
                'home_office_return_case_id' => $this->record->id,
                'asset_id' => $assignment->asset_id,
            ], [
                'status' => 'pending',
                'notes' => 'Auto-added from active employee assignment.',
            ]);
        }
    }
}