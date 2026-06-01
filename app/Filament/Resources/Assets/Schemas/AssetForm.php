<?php

namespace App\Filament\Resources\Assets\Schemas;

use App\Models\Position;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AssetForm
{
    
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
TextInput::make('asset_tag')
    ->label('Asset Tag')
    ->placeholder('Auto generated if empty')
    ->helperText('Consumable assets do not get asset tags.')
    ->unique(ignoreRecord: true)
    ->maxLength(100)
    ->disabled(fn (): bool => ! auth()->user()?->hasRole('Admin'))
    ->dehydrated(fn (): bool => auth()->user()?->hasRole('Admin')),

                Select::make('asset_type_id')
                    ->label('Asset Type')
                    ->relationship('assetType', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

Select::make('brand_id')
    ->label('Brand')
    ->options(fn () => \App\Models\Brand::query()
        ->orderBy('name')
        ->pluck('name', 'id')
        ->toArray())
    ->searchable()
    ->preload()
    ->live()
    ->afterStateUpdated(fn ($set) => $set('asset_model_id', null))
    ->required(),

Select::make('asset_model_id')
    ->label('Model')
    ->options(function ($get) {
        $brandId = $get('brand_id');

        if (! $brandId) {
            return [];
        }

        return \App\Models\AssetModel::query()
            ->where('brand_id', $brandId)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    })
    ->searchable()
    ->preload()
    ->required(),

                TextInput::make('serial_number')
                    ->label('Serial Number')
                    ->maxLength(255),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'available' => 'Available',
                        'assigned' => 'Assigned',
                        'in_storage' => 'In Storage',
                        'in_repair' => 'In Repair',
                        'damaged' => 'Damaged',
                        'lost' => 'Lost',
                        'retired' => 'Retired',
                    ])
                    ->required()
                    ->default('available'),

                Select::make('condition')
                    ->label('Condition')
                    ->options([
                        'new' => 'New',
                        'good' => 'Good',
                        'used' => 'Used',
                        'needs_check' => 'Needs Check',
                        'damaged' => 'Damaged',
                        'broken' => 'Broken',
                        'missing_serial' => 'Missing Serial',
                    ])
                    ->required()
                    ->default('good'),

                Select::make('current_location_id')
                    ->label('Current Location')
                    ->relationship('currentLocation', 'name')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required()
                    ->afterStateUpdated(function ($set) {
                        $set('current_position_id', null);
                    }),

                Select::make('current_position_id')
                    ->label('Current Position')
                    ->options(function ($get) {
                        $locationId = $get('current_location_id');

                        if (! $locationId) {
                            return [];
                        }

                        return Position::query()
                            ->where('location_id', $locationId)
                            ->orderBy('code')
                            ->pluck('code', 'id')
                            ->toArray();
                    })
                    ->searchable()
                    ->preload(),

                Select::make('current_employee_id')
    ->label('Current Employee')
    ->relationship('currentEmployee', 'full_name')
    ->searchable()
    ->preload()
    ->live()
    ->afterStateUpdated(function ($state, $set): void {
        if ($state) {
            $set('status', 'assigned');
            $set('current_position_id', null);
        }
    }),

                Textarea::make('notes')
                    ->label('Notes')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}