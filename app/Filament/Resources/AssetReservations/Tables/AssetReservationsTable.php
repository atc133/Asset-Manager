<?php

namespace App\Filament\Resources\AssetReservations\Tables;

use App\Models\AssetAssignment;
use App\Models\AssetReservation;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AssetReservationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('asset.asset_tag')
                    ->label('Asset')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('asset.assetModel.name')
                    ->label('Model')
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('employee.full_name')
                    ->label('Reserved For')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('reservedBy.name')
                    ->label('Reserved By')
                    ->placeholder('System')
                    ->toggleable(),

                TextColumn::make('reserved_from')
                    ->label('From')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('reserved_until')
                    ->label('Until')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('-')
                    ->color(fn ($record): string => $record->reserved_until && $record->reserved_until->isPast() && $record->status === 'active'
                        ? 'danger'
                        : 'gray'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable()
                    ->color(fn (?string $state): string => match ($state) {
                        'active' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),

                SelectFilter::make('employee_id')
                    ->label('Employee')
                    ->relationship('employee', 'full_name')
                    ->searchable()
                    ->preload(),

                Filter::make('expired')
                    ->label('Expired Active Reservations')
                    ->query(fn (Builder $query): Builder => $query
                        ->where('status', 'active')
                        ->whereNotNull('reserved_until')
                        ->whereDate('reserved_until', '<', now())),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ActionGroup::make([
                    Action::make('convert_to_assignment')
                        ->label('Convert to Assignment')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn (AssetReservation $record): bool => $record->status === 'active')
                        ->action(function (AssetReservation $record): void {
                            AssetAssignment::create([
                                'asset_id' => $record->asset_id,
                                'assignment_type' => 'employee',
                                'employee_id' => $record->employee_id,
                                'assigned_from' => now(),
                                'status' => 'active',
                                'notes' => 'Converted from reservation #' . $record->id,
                            ]);

                            $record->update([
                                'status' => 'completed',
                            ]);

                            Notification::make()
                                ->title('Reservation converted')
                                ->body('The asset was assigned to the employee.')
                                ->success()
                                ->send();
                        }),

                    Action::make('cancel')
                        ->label('Cancel Reservation')
                        ->icon('heroicon-o-x-circle')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->visible(fn (AssetReservation $record): bool => $record->status === 'active')
                        ->action(function (AssetReservation $record): void {
                            $record->update([
                                'status' => 'cancelled',
                            ]);

                            $record->asset?->update([
                                'status' => 'available',
                            ]);

                            Notification::make()
                                ->title('Reservation cancelled')
                                ->success()
                                ->send();
                        }),

                    EditAction::make(),
                ])
                    ->label('Actions')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->button(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}