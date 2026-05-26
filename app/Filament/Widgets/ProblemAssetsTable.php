<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Assets\AssetResource;
use App\Models\Asset;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class ProblemAssetsTable extends BaseWidget
{
    protected static ?string $heading = 'Problem Assets';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Asset::query()
                    ->with([
                        'assetType',
                        'currentLocation',
                        'currentEmployee',
                        'currentPosition',
                    ])
                    ->where(function (Builder $query): void {
                        $query
                            ->whereNull('serial_number')
                            ->orWhere('serial_number', '')
                            ->orWhere('serial_number', '-')
                            ->orWhere('serial_number', '????')
                            ->orWhere('serial_number', 'NO LABEL')
                            ->orWhereNull('current_location_id')
                            ->orWhereIn('status', [
                                'in_repair',
                                'damaged',
                                'lost',
                                'retired',
                            ])
                            ->orWhereIn('condition', [
                                'needs_check',
                                'damaged',
                                'broken',
                                'missing_serial',
                            ])
                            ->orWhere(function (Builder $query): void {
                                $query
                                    ->where('status', 'assigned')
                                    ->whereNull('current_employee_id')
                                    ->whereNull('current_position_id');
                            });
                    })
            )
            ->columns([
                TextColumn::make('asset_tag')
                    ->label('Asset Tag')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('assetType.name')
                    ->label('Type')
                    ->sortable(),

                TextColumn::make('serial_number')
                    ->label('Serial')
                    ->placeholder('Missing')
                    ->limit(18)
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('condition')
                    ->label('Condition')
                    ->badge()
                    ->sortable(),

                TextColumn::make('currentLocation.name')
                    ->label('Location')
                    ->placeholder('No Location')
                    ->limit(20),

                TextColumn::make('currentEmployee.full_name')
                    ->label('Employee')
                    ->placeholder('-')
                    ->limit(20),

                TextColumn::make('currentPosition.code')
                    ->label('Position')
                    ->placeholder('-'),

                TextColumn::make('problem')
                    ->label('Problem')
                    ->state(function (Asset $record): string {
                        if (
                            blank($record->serial_number)
                            || in_array($record->serial_number, ['-', '????', 'NO LABEL'], true)
                        ) {
                            return 'Missing Serial';
                        }

                        if ($record->current_location_id === null) {
                            return 'No Location';
                        }

                        if ($record->status === 'assigned'
                            && $record->current_employee_id === null
                            && $record->current_position_id === null
                        ) {
                            return 'Assigned but no holder';
                        }

                        if ($record->status === 'in_repair') {
                            return 'In Repair';
                        }

                        if ($record->status === 'lost') {
                            return 'Lost';
                        }

                        if ($record->status === 'retired') {
                            return 'Retired';
                        }

                        if (in_array($record->condition, ['needs_check', 'damaged', 'broken', 'missing_serial'], true)) {
                            return 'Bad Condition';
                        }

                        return 'Check Needed';
                    })
                    ->badge(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->recordActions([
                Action::make('open')
                    ->label('Open')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn (Asset $record): string => AssetResource::getUrl('edit', [
                        'record' => $record,
                    ])),
            ]);
    }
}