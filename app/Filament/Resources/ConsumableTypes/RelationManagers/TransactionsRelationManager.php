<?php

namespace App\Filament\Resources\ConsumableTypes\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'transactions';

    protected static ?string $title = 'Stock Movement History';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('type')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => str($state)->replace('_', ' ')->title()->toString())
                    ->color(fn (?string $state): string => match ($state) {
                        'stock_in' => 'success',
                        'stock_out' => 'warning',
                        'adjustment' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('quantity')
                    ->label('Quantity')
                    ->sortable(),

                TextColumn::make('stock_before')
                    ->label('Before')
                    ->sortable(),

                TextColumn::make('stock_after')
                    ->label('After')
                    ->sortable(),

                TextColumn::make('createdBy.name')
                    ->label('User')
                    ->placeholder('System'),

                TextColumn::make('notes')
                    ->label('Notes')
                    ->wrap()
                    ->placeholder('-'),
            ])
            ->defaultSort('created_at', 'desc');
    }
}