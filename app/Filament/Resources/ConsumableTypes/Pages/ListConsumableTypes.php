<?php

namespace App\Filament\Resources\ConsumableTypes\Pages;

use App\Filament\Resources\ConsumableTypes\ConsumableTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListConsumableTypes extends ListRecords
{
    protected static string $resource = ConsumableTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
