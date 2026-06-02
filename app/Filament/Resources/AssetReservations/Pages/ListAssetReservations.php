<?php

namespace App\Filament\Resources\AssetReservations\Pages;

use App\Filament\Resources\AssetReservations\AssetReservationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAssetReservations extends ListRecords
{
    protected static string $resource = AssetReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
