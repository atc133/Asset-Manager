<?php

namespace App\Filament\Resources\AssetModels\Pages;

use App\Filament\Resources\AssetModels\AssetModelResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAssetModel extends EditRecord
{
    protected static string $resource = AssetModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
