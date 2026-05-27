<?php

namespace App\Filament\Resources\AssetAssignments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class AssetAssignmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('asset.asset_tag')
                    ->label('Asset Tag')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('asset.serial_number')
                    ->label('Serial Number')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('assignment_type')
                    ->label('Type')
                    ->badge()
                    ->sortable(),

                TextColumn::make('employee.full_name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('position.code')
                    ->label('Position')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('location.name')
                    ->label('Location')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('assigned_from')
                    ->label('From')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('assigned_until')
                    ->label('Until')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),
            ])
            ->filters([
    SelectFilter::make('status')
        ->label('Status')
        ->options([
            'active' => 'Active',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ]),

    SelectFilter::make('assignment_type')
        ->label('Type')
        ->options([
            'employee' => 'Employee',
            'position' => 'Position',
            'storage' => 'Storage',
            'repair' => 'Repair',
            'lost' => 'Lost',
        ]),

    SelectFilter::make('employee_id')
        ->label('Employee')
        ->relationship('employee', 'full_name')
        ->searchable()
        ->preload(),

    SelectFilter::make('position_id')
        ->label('Position')
        ->relationship('position', 'code')
        ->searchable()
        ->preload(),

    SelectFilter::make('location_id')
        ->label('Location')
        ->relationship('location', 'name')
        ->searchable()
        ->preload(),

    SelectFilter::make('asset_id')
    ->label('Asset')
    ->options(fn () => \App\Models\Asset::query()
        ->whereNotNull('asset_tag')
        ->where('asset_tag', '!=', '')
        ->orderBy('asset_tag')
        ->pluck('asset_tag', 'id')
        ->toArray())
    ->searchable()
    ->preload(),
])
            ->defaultSort('assigned_from', 'desc')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}