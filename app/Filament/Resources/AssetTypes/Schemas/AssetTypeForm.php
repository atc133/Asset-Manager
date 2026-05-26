<?php

namespace App\Filament\Resources\AssetTypes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AssetTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Name')
                    ->placeholder('Example: PC, Laptop, Monitor')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(100),

                TextInput::make('code')
                    ->label('Asset Tag Code')
                    ->placeholder('Example: PC, LAP, MON')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(20)
                    ->dehydrateStateUsing(fn (?string $state): ?string => $state ? strtoupper(trim($state)) : null),

                Toggle::make('requires_serial')
                    ->label('Requires Serial Number')
                    ->default(true),

                Toggle::make('is_consumable')
                    ->label('Is Consumable / Quantity Item')
                    ->default(false),
            ]);
    }
}