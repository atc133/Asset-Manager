<?php

namespace App\Filament\Resources\HomeOfficeReturnCases\Pages;

use App\Filament\Resources\HomeOfficeReturnCases\HomeOfficeReturnCaseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHomeOfficeReturnCases extends ListRecords
{
    protected static string $resource = HomeOfficeReturnCaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
