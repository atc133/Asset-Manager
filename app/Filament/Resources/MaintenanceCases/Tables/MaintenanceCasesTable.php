<?php

namespace App\Filament\Resources\MaintenanceCases\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MaintenanceCasesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('asset.asset_tag')
                    ->label('Asset')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('asset.serial_number')
                    ->label('Serial')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('issue')
                    ->label('Issue')
                    ->searchable()
                    ->limit(35),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('handledBy.name')
                    ->label('Handled By')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('opened_at')
                    ->label('Opened')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('closed_at')
                    ->label('Closed')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'open' => 'Open',
                        'in_progress' => 'In Progress',
                        'fixed' => 'Fixed',
                        'cannot_fix' => 'Cannot Fix',
                        'retired' => 'Retired',
                    ]),
            ])
            ->defaultSort('opened_at', 'desc')
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