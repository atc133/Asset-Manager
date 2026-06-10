<?php

namespace App\Filament\Resources\ConsumableTypes\Tables;

use App\Models\ConsumableTransaction;
use App\Models\ConsumableType;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Select;

class ConsumableTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Consumable')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category')
                    ->label('Category')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->placeholder('-'),

                TextColumn::make('current_stock')
                    ->label('Current Stock')
                    ->sortable()
                    ->badge()
                    ->color(fn (ConsumableType $record): string => $record->isLowStock() ? 'danger' : 'success'),

                TextColumn::make('minimum_stock')
                    ->label('Minimum')
                    ->sortable(),

                TextColumn::make('stock_status')
                    ->label('Status')
                    ->state(fn (ConsumableType $record): string => $record->isLowStock() ? 'Low Stock' : 'OK')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'Low Stock' ? 'danger' : 'success'),

                IconColumn::make('active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('description')
                    ->label('Description')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Category')
                    ->options(fn () => ConsumableType::query()
                        ->whereNotNull('category')
                        ->where('category', '!=', '')
                        ->distinct()
                        ->orderBy('category')
                        ->pluck('category', 'category')
                        ->toArray())
                    ->searchable(),

                Filter::make('low_stock')
                    ->label('Low Stock')
                    ->query(fn (Builder $query): Builder => $query
                        ->whereColumn('current_stock', '<=', 'minimum_stock')),

                Filter::make('active')
                    ->label('Active Only')
                    ->query(fn (Builder $query): Builder => $query->where('active', true)),
            ])
            ->defaultSort('name')
            ->recordActions([
                ActionGroup::make([
                    Action::make('stock_in')
                        ->label('Stock In')
                        ->icon('heroicon-o-plus-circle')
                        ->color('success')
                        ->form([
                            TextInput::make('quantity')
                                ->label('Quantity')
                                ->numeric()
                                ->minValue(1)
                                ->required(),

                            Textarea::make('notes')
                                ->label('Notes')
                                ->rows(3),
                        ])
                        ->action(function (ConsumableType $record, array $data): void {
                            ConsumableTransaction::create([
                                'consumable_type_id' => $record->id,
                                'type' => 'stock_in',
                                'quantity' => (int) $data['quantity'],
                                'notes' => $data['notes'] ?? null,
                            ]);

                            Notification::make()
                                ->title('Stock added')
                                ->success()
                                ->send();
                        }),

                    Action::make('stock_out')
    ->label('Stock Out / Assign')
    ->icon('heroicon-o-minus-circle')
    ->color('warning')
    ->form([
        Select::make('assignment_type')
            ->label('Assign To')
            ->options([
                'none' => 'No Assignment',
                'employee' => 'Employee',
                'position' => 'Position',
            ])
            ->default('none')
            ->required()
            ->live(),

        Select::make('employee_id')
    ->label('Employee')
    ->options(
        \App\Models\Employee::query()
            ->orderBy('full_name')
            ->pluck('full_name', 'id')
            ->toArray()
    )
    ->searchable()
    ->visible(fn ($get): bool => $get('assignment_type') === 'employee')
    ->required(fn ($get): bool => $get('assignment_type') === 'employee'),

        Select::make('position_id')
    ->label('Position')
    ->options(
        \App\Models\Position::query()
            ->orderBy('code')
            ->pluck('code', 'id')
            ->toArray()
    )
    ->searchable()
    ->visible(fn ($get): bool => $get('assignment_type') === 'position')
    ->required(fn ($get): bool => $get('assignment_type') === 'position'),

        TextInput::make('quantity')
            ->label('Quantity')
            ->numeric()
            ->minValue(1)
            ->maxValue(fn (ConsumableType $record): int => max(0, $record->current_stock))
            ->required(),

        Textarea::make('notes')
            ->label('Notes')
            ->rows(3),
    ])
    ->action(function (ConsumableType $record, array $data): void {
        if ((int) $data['quantity'] > $record->current_stock) {
            Notification::make()
                ->title('Not enough stock')
                ->body('You cannot remove more items than the current stock.')
                ->danger()
                ->send();

            return;
        }

        ConsumableTransaction::create([
            'consumable_type_id' => $record->id,
            'type' => 'stock_out',
            'assignment_type' => $data['assignment_type'] !== 'none' ? $data['assignment_type'] : null,
            'employee_id' => ($data['assignment_type'] ?? null) === 'employee' ? $data['employee_id'] : null,
            'position_id' => ($data['assignment_type'] ?? null) === 'position' ? $data['position_id'] : null,
            'quantity' => (int) $data['quantity'],
            'notes' => $data['notes'] ?? null,
        ]);

        Notification::make()
            ->title('Stock removed')
            ->body('Consumable stock was updated successfully.')
            ->success()
            ->send();
    }),

                    Action::make('adjust_stock')
                        ->label('Adjust Stock')
                        ->icon('heroicon-o-adjustments-horizontal')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->form([
                            TextInput::make('quantity')
                                ->label('New Stock Value')
                                ->numeric()
                                ->minValue(0)
                                ->required(),

                            Textarea::make('notes')
                                ->label('Reason')
                                ->rows(3)
                                ->required(),
                        ])
                        ->action(function (ConsumableType $record, array $data): void {
                            ConsumableTransaction::create([
                                'consumable_type_id' => $record->id,
                                'type' => 'adjustment',
                                'quantity' => (int) $data['quantity'],
                                'notes' => $data['notes'] ?? null,
                            ]);

                            Notification::make()
                                ->title('Stock adjusted')
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