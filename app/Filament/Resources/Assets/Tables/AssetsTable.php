<?php

namespace App\Filament\Resources\Assets\Tables;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\Employee;
use App\Models\Location;
use App\Models\Position;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Filament\Forms\Components\DatePicker;

class AssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('asset_tag')
                    ->label('Asset Tag')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('assetType.name')
                    ->label('Type')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('serial_number')
                    ->label('Serial')
                    ->searchable()
                    ->sortable()
                    ->limit(18)
                    ->placeholder('-'),

                TextColumn::make('received_at')
                    ->label('Received')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('-'),
                
                TextColumn::make('warranty_until')
                    ->label('Warranty')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('asset_age')
                    ->label('Age')
                    ->state(function ($record) {

                        if (! $record->received_at) {
                            return '-';
                            }

                            return $record->received_at->diffForHumans();
                        }),

                TextColumn::make('expected_replacement_at')
                    ->label('Replacement')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('currentLocation.name')
                    ->label('Location')
                    ->searchable()
                    ->sortable()
                    ->limit(18)
                    ->placeholder('-'),

                TextColumn::make('currentEmployee.full_name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable()
                    ->limit(20)
                    ->placeholder('-'),

                TextColumn::make('currentPosition.code')
                    ->label('Position')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('brand.name')
    ->label('Brand')
    ->badge()
    ->searchable()
    ->sortable()
    ->placeholder('-'),
                    

                TextColumn::make('assetModel.name')
    ->label('Model')
    ->searchable()
    ->sortable()
    ->placeholder('-'),


                TextColumn::make('condition')
                    ->label('Condition')
                    ->badge()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'available' => 'Available',
                        'assigned' => 'Assigned',
                        'in_storage' => 'In Storage',
                        'in_repair' => 'In Repair',
                        'damaged' => 'Damaged',
                        'lost' => 'Lost',
                        'retired' => 'Retired',
                    ]),

                SelectFilter::make('asset_type_id')
                    ->label('Asset Type')
                    ->relationship('assetType', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('current_location_id')
                    ->label('Location')
                    ->relationship('currentLocation', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('current_employee_id')
                    ->label('Employee')
                    ->relationship('currentEmployee', 'full_name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('condition')
                    ->label('Condition')
                    ->options([
                        'new' => 'New',
                        'good' => 'Good',
                        'used' => 'Used',
                        'needs_check' => 'Needs Check',
                        'damaged' => 'Damaged',
                        'broken' => 'Broken',
                        'missing_serial' => 'Missing Serial',
                    ]),
                    SelectFilter::make('brand_id')
    ->label('Brand')
    ->relationship('brand', 'name')
    ->searchable()
    ->preload(),

SelectFilter::make('asset_model_id')
    ->label('Model')
    ->relationship('assetModel', 'name')
    ->searchable()
    ->preload(),
Filter::make('received_at')
    ->label('Received Date')
    ->form([
        DatePicker::make('received_from')
            ->label('Received From')
            ->native(false)
            ->displayFormat('d/m/Y'),

        DatePicker::make('received_until')
            ->label('Received Until')
            ->native(false)
            ->displayFormat('d/m/Y'),
    ])
    ->query(function (Builder $query, array $data): Builder {
        return $query
            ->when(
                $data['received_from'] ?? null,
                fn (Builder $query, $date): Builder => $query->whereDate('received_at', '>=', $date),
            )
            ->when(
                $data['received_until'] ?? null,
                fn (Builder $query, $date): Builder => $query->whereDate('received_at', '<=', $date),
            );
    }),
                Filter::make('missing_serial')
                    ->label('Missing Serial')
                    ->query(fn (Builder $query): Builder => $query
                        ->where(function (Builder $query): void {
                            $query
                                ->whereNull('serial_number')
                                ->orWhere('serial_number', '')
                                ->orWhere('serial_number', '-')
                                ->orWhere('serial_number', '????')
                                ->orWhere('serial_number', 'NO LABEL');
                        })),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('assign_to_employee')
                        ->visible(fn () => auth()->user()?->can('assign_assets') ?? false)
                        ->label('Assign to Employee')
                        ->icon('heroicon-o-user')
                        ->modalHeading('Assign asset to employee')
                        ->form([
                            Select::make('employee_id')
                                ->label('Employee')
                                ->options(fn () => Employee::query()
                                    ->orderBy('full_name')
                                    ->pluck('full_name', 'id')
                                    ->toArray())
                                ->searchable()
                                ->required(),

                            Textarea::make('notes')
                                ->label('Notes')
                                ->rows(3),
                        ])
                        ->action(function (Asset $record, array $data): void {
                            AssetAssignment::create([
                                'asset_id' => $record->id,
                                'assignment_type' => 'employee',
                                'employee_id' => $data['employee_id'],
                                'assigned_from' => now(),
                                'status' => 'active',
                                'notes' => $data['notes'] ?? null,
                            ]);
                        }),

                    Action::make('assign_to_position')
                        ->visible(fn () => auth()->user()?->can('assign_assets') ?? false)
                        ->label('Assign to Position')
                        ->icon('heroicon-o-computer-desktop')
                        ->modalHeading('Assign asset to position')
                        ->form([
                            Select::make('position_id')
                                ->label('Position')
                                ->options(fn () => Position::query()
                                    ->with('location')
                                    ->orderBy('code')
                                    ->get()
                                    ->mapWithKeys(fn (Position $position) => [
                                        $position->id => $position->code . ' - ' . ($position->location?->name ?? 'No location'),
                                    ])
                                    ->toArray())
                                ->searchable()
                                ->required(),

                            Textarea::make('notes')
                                ->label('Notes')
                                ->rows(3),
                        ])
                        ->action(function (Asset $record, array $data): void {
                            AssetAssignment::create([
                                'asset_id' => $record->id,
                                'assignment_type' => 'position',
                                'position_id' => $data['position_id'],
                                'assigned_from' => now(),
                                'status' => 'active',
                                'notes' => $data['notes'] ?? null,
                            ]);
                        }),

                    Action::make('move_to_storage')
                        ->visible(fn () => auth()->user()?->can('return_assets') ?? false)
                        ->label('Move to Storage')
                        ->icon('heroicon-o-archive-box')
                        ->modalHeading('Move asset to storage')
                        ->form([
                            Select::make('location_id')
                                ->label('Storage Location')
                                ->options(fn () => Location::query()
                                    ->where('type', 'storage')
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->toArray())
                                ->searchable()
                                ->required(),

                            Textarea::make('notes')
                                ->label('Notes')
                                ->rows(3),
                        ])
                        ->action(function (Asset $record, array $data): void {
                            AssetAssignment::create([
                                'asset_id' => $record->id,
                                'assignment_type' => 'storage',
                                'location_id' => $data['location_id'],
                                'assigned_from' => now(),
                                'status' => 'active',
                                'notes' => $data['notes'] ?? null,
                            ]);
                        }),

                    Action::make('return_asset')
                        ->visible(fn () => auth()->user()?->can('return_assets') ?? false)
                        ->label('Return Asset')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->modalHeading('Return asset')
                        ->form([
                            Select::make('destination')
                                ->label('Return Destination')
                                ->options([
                                    'storage' => 'Storage',
                                    'repair' => 'Repair / Needs Check',
                                    'damaged' => 'Damaged',
                                    'lost' => 'Lost',
                                ])
                                ->required()
                                ->default('storage')
                                ->live(),

                            Select::make('location_id')
                                ->label('Return Location')
                                ->options(fn () => Location::query()
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->toArray())
                                ->searchable()
                                ->required(),

                            Select::make('condition')
                                ->label('Condition on Return')
                                ->options([
                                    'new' => 'New',
                                    'good' => 'Good',
                                    'used' => 'Used',
                                    'needs_check' => 'Needs Check',
                                    'damaged' => 'Damaged',
                                    'broken' => 'Broken',
                                    'missing_serial' => 'Missing Serial',
                                ])
                                ->required()
                                ->default('used'),

                            Textarea::make('notes')
                                ->label('Return Notes')
                                ->rows(3)
                                ->placeholder('Example: Returned by employee, checked by IT, missing power cable...'),
                        ])
                        ->action(function (Asset $record, array $data): void {
                            $assignmentType = match ($data['destination']) {
                                'repair' => 'repair',
                                'damaged' => 'repair',
                                'lost' => 'lost',
                                default => 'storage',
                            };

                            $status = match ($data['destination']) {
                                'repair' => 'in_repair',
                                'damaged' => 'damaged',
                                'lost' => 'lost',
                                default => 'in_storage',
                            };

                            AssetAssignment::create([
                                'asset_id' => $record->id,
                                'assignment_type' => $assignmentType,
                                'location_id' => $data['location_id'],
                                'assigned_from' => now(),
                                'status' => 'active',
                                'notes' => $data['notes'] ?? null,
                            ]);

                            $record->update([
                                'condition' => $data['condition'],
                                'status' => $status,
                            ]);
                        }),

                    Action::make('send_to_repair')
                        ->visible(fn () => auth()->user()?->can('manage_maintenance') ?? false)
                        ->label('Send to Repair')
                        ->icon('heroicon-o-wrench-screwdriver')
                        ->modalHeading('Send asset to repair')
                        ->form([
                            Select::make('location_id')
                                ->label('Repair / Service Location')
                                ->options(fn () => Location::query()
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->toArray())
                                ->searchable()
                                ->required(),

                            Textarea::make('notes')
                                ->label('Problem / Notes')
                                ->rows(3)
                                ->required(),
                        ])
                        ->action(function (Asset $record, array $data): void {
                            AssetAssignment::create([
                                'asset_id' => $record->id,
                                'assignment_type' => 'repair',
                                'location_id' => $data['location_id'],
                                'assigned_from' => now(),
                                'status' => 'active',
                                'notes' => $data['notes'] ?? null,
                            ]);
                        }),

                    Action::make('open_qr_page')
    ->label('Open QR Page')
    ->icon('heroicon-o-qr-code')
    ->visible(fn (Asset $record): bool => filled($record->asset_tag))
    ->url(fn (Asset $record): string => route('assets.public.show', [
        'asset' => $record->asset_tag,
    ]))
    ->openUrlInNewTab(),

                    Action::make('print_label')
    ->label('Print QR / Barcode')
    ->icon('heroicon-o-printer')
    ->visible(fn (Asset $record): bool => filled($record->asset_tag))
    ->url(fn (Asset $record): string => route('assets.public.label', [
        'asset' => $record->asset_tag,
    ]))
    ->openUrlInNewTab(),

                    EditAction::make()
                        ->visible(fn () => auth()->user()?->can('edit_assets') ?? false),
                ])
                    ->label('Actions')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->button(),
            ])
            ->toolbarActions([
                Action::make('import_assets')
                    ->label('Import Assets')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->url(route('assets.import.form'))
                    ->openUrlInNewTab()
                    ->visible(fn () => auth()->user()?->can('create_assets') ?? false),

                BulkActionGroup::make([
                    BulkAction::make('move_selected_to_storage')
                        ->label('Move Selected to Storage')
                        ->icon('heroicon-o-archive-box')
                        ->visible(fn () => auth()->user()?->can('return_assets') ?? false)
                        ->form([
                            Select::make('location_id')
                                ->label('Storage Location')
                                ->options(fn () => Location::query()
                                    ->where('type', 'storage')
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->toArray())
                                ->searchable()
                                ->required(),

                            Textarea::make('notes')
                                ->label('Notes')
                                ->rows(3),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            foreach ($records as $record) {
                                AssetAssignment::create([
                                    'asset_id' => $record->id,
                                    'assignment_type' => 'storage',
                                    'location_id' => $data['location_id'],
                                    'assigned_from' => now(),
                                    'status' => 'active',
                                    'notes' => $data['notes'] ?? null,
                                ]);
                            }
                        }),

                    BulkAction::make('send_selected_to_repair')
                        ->label('Send Selected to Repair')
                        ->icon('heroicon-o-wrench-screwdriver')
                        ->visible(fn () => auth()->user()?->can('manage_maintenance') ?? false)
                        ->form([
                            Select::make('location_id')
                                ->label('Repair / Service Location')
                                ->options(fn () => Location::query()
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->toArray())
                                ->searchable()
                                ->required(),

                            Textarea::make('notes')
                                ->label('Problem / Notes')
                                ->rows(3)
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            foreach ($records as $record) {
                                AssetAssignment::create([
                                    'asset_id' => $record->id,
                                    'assignment_type' => 'repair',
                                    'location_id' => $data['location_id'],
                                    'assigned_from' => now(),
                                    'status' => 'active',
                                    'notes' => $data['notes'] ?? null,
                                ]);
                            }
                        }),

                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('delete_assets') ?? false),
                ]),
            ]);
    }
}