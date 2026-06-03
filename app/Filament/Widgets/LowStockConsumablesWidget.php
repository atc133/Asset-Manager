<?php

namespace App\Filament\Widgets;

use App\Models\ConsumableType;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LowStockConsumablesWidget extends TableWidget
{
    protected static ?string $heading = 'Low Stock Consumables';

    protected static ?int $sort = -3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => ConsumableType::query()
                ->where('active', true)
                ->whereColumn('current_stock', '<=', 'minimum_stock')
                ->orderBy('current_stock'))
            ->columns([
                TextColumn::make('name')
                    ->label('Consumable')
                    ->searchable(),

                TextColumn::make('category')
                    ->label('Category')
                    ->badge()
                    ->placeholder('-'),

                TextColumn::make('current_stock')
                    ->label('Current')
                    ->badge()
                    ->color('danger'),

                TextColumn::make('minimum_stock')
                    ->label('Minimum'),

                TextColumn::make('reorder_status')
                    ->label('Status')
                    ->state(fn (): string => 'Reorder Needed')
                    ->badge()
                    ->color('danger'),
            ]);
    }
}