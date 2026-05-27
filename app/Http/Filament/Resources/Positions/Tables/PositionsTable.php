<?php

namespace App\Filament\Resources\Positions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Tables\Filters\SelectFilter;

class PositionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Position')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('location.name')
                    ->label('Location')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('floor')
                    ->label('Floor')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('room')
                    ->label('Room')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
    SelectFilter::make('location_id')
        ->label('Location')
        ->relationship('location', 'name')
        ->searchable()
        ->preload(),
])
            ->recordActions([
                EditAction::make(),
                Action::make('print_position_label')
    ->label('Print QR / Barcode')
    ->icon('heroicon-o-printer')
    ->url(fn ($record): string => route('positions.public.label', [
        'position' => $record->code,
    ]))
    ->openUrlInNewTab(),

Action::make('open_position_page')
    ->label('Open Position Page')
    ->icon('heroicon-o-qr-code')
    ->url(fn ($record): string => route('positions.public.show', [
        'position' => $record->code,
    ]))
    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}