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
                    ->label('Asset Tag')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Asset tag copied')
                    ->copyMessageDuration(1500)
                    ->placeholder('-'),

                TextColumn::make('asset.assetType.name')
                    ->label('Asset Type')
                    ->badge()
                    ->color('info')
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('assignment_type')
                    ->label('Type')
                    ->badge()
                    ->icon(fn (?string $state): string => match ($state) {
                        'employee' => 'heroicon-m-user',
                        'position' => 'heroicon-m-map-pin',
                        'storage' => 'heroicon-m-archive-box',
                        'repair' => 'heroicon-m-wrench-screwdriver',
                        'lost' => 'heroicon-m-exclamation-triangle',
                        default => 'heroicon-m-arrow-path',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'employee' => 'success',
                        'position' => 'info',
                        'storage' => 'gray',
                        'repair' => 'warning',
                        'lost' => 'danger',
                        default => 'primary',
                    }),

                TextColumn::make('holder')
                    ->label('Assigned To / Location')
                    ->state(function (AssetAssignment $record): string {
                        return $record->employee?->full_name
                            ?? $record->position?->code
                            ?? $record->location?->name
                            ?? '-';
                    })
                    ->searchable(query: function ($query, string $search) {
                        return $query
                            ->whereHas('employee', fn ($q) => $q->where('full_name', 'like', "%{$search}%"))
                            ->orWhereHas('position', fn ($q) => $q->where('code', 'like', "%{$search}%"))
                            ->orWhereHas('location', fn ($q) => $q->where('name', 'like', "%{$search}%"));
                    })
                    ->icon('heroicon-m-user-circle'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->icon(fn (?string $state): string => match ($state) {
                        'active' => 'heroicon-m-check-circle',
                        'completed' => 'heroicon-m-check-badge',
                        'cancelled' => 'heroicon-m-x-circle',
                        default => 'heroicon-m-clock',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'active' => 'success',
                        'completed' => 'gray',
                        'cancelled' => 'danger',
                        default => 'warning',
                    }),

                TextColumn::make('location.name')
                    ->label('Location')
                    ->badge()
                    ->color('info')
                    ->placeholder('-')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('assigned_from')
                    ->label('From')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('assigned_until')
                    ->label('Until')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Active')
                    ->sortable()
                    ->toggleable(),
            ])
            ->emptyStateHeading('No recent assignments')
            ->emptyStateDescription('Asset assignment activity will appear here.')
            ->emptyStateIcon('heroicon-o-arrows-right-left')
            ->defaultSort('assigned_from', 'desc');
    }
}