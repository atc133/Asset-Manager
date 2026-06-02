<?php

namespace App\Filament\Resources\AssetReservations\Pages;

use App\Filament\Resources\AssetReservations\AssetReservationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAssetReservation extends EditRecord
{
    protected static string $resource = AssetReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
