<?php

namespace App\Filament\Resources\MaintenanceCases\Pages;

use App\Filament\Resources\MaintenanceCases\MaintenanceCaseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMaintenanceCases extends ListRecords
{
    protected static string $resource = MaintenanceCaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
