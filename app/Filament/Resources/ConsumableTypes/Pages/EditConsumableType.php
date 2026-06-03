<?php

namespace App\Filament\Resources\ConsumableTypes\Pages;

use App\Filament\Resources\ConsumableTypes\ConsumableTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditConsumableType extends EditRecord
{
    protected static string $resource = ConsumableTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
