<?php

namespace App\Filament\Resources\AssetAssignments\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AssetAssignmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('asset_id')
                    ->label('Asset')
                    ->relationship('asset', 'asset_tag')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->asset_tag} - {$record->brand} {$record->model} - {$record->serial_number}")
                    ->searchable(['asset_tag', 'serial_number', 'brand', 'model'])
                    ->preload()
                    ->required(),

                Select::make('assignment_type')
                    ->label('Assignment Type')
                    ->options([
                        'employee' => 'Employee',
                        'position' => 'Position',
                        'storage' => 'Storage',
                        'repair' => 'Repair',
                        'retired' => 'Retired',
                        'lost' => 'Lost',
                    ])
                    ->required()
                    ->live(),

                Select::make('employee_id')
                    ->label('Employee')
                    ->relationship('employee', 'full_name')
                    ->searchable()
                    ->preload()
                    ->visible(fn ($get) => $get('assignment_type') === 'employee')
                    ->required(fn ($get) => $get('assignment_type') === 'employee'),

                Select::make('position_id')
                    ->label('Position')
                    ->relationship('position', 'code')
                    ->searchable()
                    ->preload()
                    ->visible(fn ($get) => $get('assignment_type') === 'position')
                    ->required(fn ($get) => $get('assignment_type') === 'position'),

                Select::make('location_id')
                    ->label('Location')
                    ->relationship('location', 'name')
                    ->searchable()
                    ->preload()
                    ->visible(fn ($get) => in_array($get('assignment_type'), ['storage', 'repair', 'retired', 'lost']))
                    ->required(fn ($get) => in_array($get('assignment_type'), ['storage', 'repair', 'retired', 'lost'])),

                DateTimePicker::make('assigned_from')
                    ->label('Assigned From')
                    ->seconds(false)
                    ->default(now())
                    ->required(),

                DateTimePicker::make('assigned_until')
                    ->label('Assigned Until')
                    ->seconds(false),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required()
                    ->default('active'),

                Textarea::make('notes')
                    ->label('Notes')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}