<?php

namespace App\Filament\Resources\MaintenanceCases\Pages;

use App\Filament\Resources\MaintenanceCases\MaintenanceCaseResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMaintenanceCase extends EditRecord
{
    protected static string $resource = MaintenanceCaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
