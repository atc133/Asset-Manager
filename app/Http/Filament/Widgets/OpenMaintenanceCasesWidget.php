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
                    ->label('Asset')
                    ->searchable(),

                TextColumn::make('issue')
                    ->label('Issue')
                    ->limit(45)
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),

                TextColumn::make('handledBy.name')
                    ->label('Handled By')
                    ->placeholder('-'),

                TextColumn::make('opened_at')
                    ->label('Opened')
                    ->dateTime(),

                TextColumn::make('closed_at')
                    ->label('Closed')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}