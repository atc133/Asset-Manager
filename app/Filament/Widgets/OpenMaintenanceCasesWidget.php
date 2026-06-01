<?php

namespace App\Filament\Widgets;

use App\Models\MaintenanceCase;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class OpenMaintenanceCasesWidget extends TableWidget
{
    protected static ?string $heading = 'Open Maintenance Cases';

    protected static ?int $sort = -4;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                MaintenanceCase::query()
                    ->with(['asset', 'handledBy'])
                    ->whereIn('status', ['open', 'in_progress'])
                    ->latest('opened_at')
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

                TextColumn::make('issue')
                    ->label('Issue')
                    ->limit(55)
                    ->tooltip(fn (MaintenanceCase $record): ?string => $record->issue)
                    ->searchable()
                    ->wrap(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->icon(fn (?string $state): string => match ($state) {
                        'open' => 'heroicon-m-exclamation-circle',
                        'in_progress' => 'heroicon-m-clock',
                        'closed' => 'heroicon-m-check-circle',
                        default => 'heroicon-m-question-mark-circle',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'open' => 'danger',
                        'in_progress' => 'warning',
                        'closed' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('handledBy.name')
                    ->label('Handled By')
                    ->icon('heroicon-m-user-circle')
                    ->placeholder('Unassigned')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('opened_at')
                    ->label('Opened')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('age')
                    ->label('Age')
                    ->state(function (MaintenanceCase $record): string {
                        if (! $record->opened_at) {
                            return '-';
                        }

                        return $record->opened_at->diffForHumans(short: true, parts: 2);
                    })
                    ->badge()
                    ->color(function (MaintenanceCase $record): string {
                        if (! $record->opened_at) {
                            return 'gray';
                        }

                        return $record->opened_at->lte(now()->subDays(30))
                            ? 'danger'
                            : ($record->opened_at->lte(now()->subDays(7)) ? 'warning' : 'success');
                    }),

                TextColumn::make('closed_at')
                    ->label('Closed')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Still open')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->emptyStateHeading('No open maintenance cases')
            ->emptyStateDescription('Great. There are no active repair or maintenance issues.')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->defaultSort('opened_at', 'desc');
    }
}