<?php

namespace App\Filament\Widgets;

use App\Models\AssetAssignment;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentAssignmentsWidget extends TableWidget
{
    protected static ?string $heading = 'Recent Asset Assignments';

    protected static ?int $sort = -5;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                AssetAssignment::query()
                    ->with(['asset', 'employee', 'position', 'location'])
                    ->latest('assigned_from')
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('asset.asset_tag')
                    ->label('Asset')
                    ->searchable(),

                TextColumn::make('assignment_type')
                    ->label('Type')
                    ->badge(),

                TextColumn::make('holder')
                    ->label('Holder')
                    ->state(function (AssetAssignment $record): string {
                        return $record->employee?->full_name
                            ?? $record->position?->code
                            ?? $record->location?->name
                            ?? '-';
                    }),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),

                TextColumn::make('assigned_from')
                    ->label('From')
                    ->dateTime(),

                TextColumn::make('assigned_until')
                    ->label('Until')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}