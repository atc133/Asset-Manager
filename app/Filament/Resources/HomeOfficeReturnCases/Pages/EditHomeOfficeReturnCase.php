<?php

namespace App\Filament\Resources\HomeOfficeReturnCases\Pages;

use App\Filament\Resources\HomeOfficeReturnCases\HomeOfficeReturnCaseResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHomeOfficeReturnCase extends EditRecord
{
    protected static string $resource = HomeOfficeReturnCaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
