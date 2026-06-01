<?php

namespace App\Filament\Resources\Employees\Tables;

use App\Filament\Resources\HomeOfficeReturnCases\HomeOfficeReturnCaseResource;
use App\Models\Asset;
use App\Models\Employee;
use App\Models\HomeOfficeReturnCase;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class EmployeesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label('Full Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('department')
                    ->label('Department')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('work_mode')
                    ->label('Work Mode')
                    ->badge()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('defaultLocation.name')
                    ->label('Default Location')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('department')
                    ->label('Department')
                    ->options(fn () => Employee::query()
                        ->whereNotNull('department')
                        ->where('department', '!=', '')
                        ->distinct()
                        ->orderBy('department')
                        ->pluck('department', 'department')
                        ->toArray())
                    ->searchable(),

                SelectFilter::make('work_mode')
                    ->label('Work Mode')
                    ->options([
                        'office' => 'Office',
                        'home_office' => 'Home Office',
                        'hybrid' => 'Hybrid',
                    ]),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ]),

                SelectFilter::make('default_location_id')
                    ->label('Default Location')
                    ->relationship('defaultLocation', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('start_home_office_return')
                        ->label('Start Return Case')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Start Home Office Return Case')
                        ->modalDescription('This will create a return case with all currently assigned assets for this employee.')
                        ->visible(fn (Employee $record): bool => in_array($record->work_mode, ['home_office', 'hybrid']))
                        ->action(function (Employee $record): void {
                            $assets = Asset::query()
                                ->where('current_employee_id', $record->id)
                                ->where('status', 'assigned')
                                ->get();

                            if ($assets->isEmpty()) {
                                Notification::make()
                                    ->title('No assigned assets found')
                                    ->body('This employee does not currently have assigned assets.')
                                    ->warning()
                                    ->send();

                                return;
                            }

                            $case = DB::transaction(function () use ($record, $assets): HomeOfficeReturnCase {
                                $case = HomeOfficeReturnCase::create([
                                    'employee_id' => $record->id,
                                    'status' => 'open',
                                    'priority' => 'normal',
                                    'assigned_to_user_id' => auth()->id(),
                                    'requested_at' => now(),
                                    'summary_notes' => 'Return case created from employee action.',
                                ]);

                                foreach ($assets as $asset) {
                                    $case->items()->create([
                                        'asset_id' => $asset->id,
                                        'status' => 'pending',
                                        'notes' => 'Automatically added from current employee assigned assets.',
                                    ]);
                                }

                                return $case;
                            });

                            Notification::make()
                                ->title('Return case created')
                                ->body('The employee assets were added to the return case.')
                                ->success()
                                ->send();

                            redirect(HomeOfficeReturnCaseResource::getUrl('edit', [
                                'record' => $case,
                            ]));
                        }),

                    Action::make('assignment_form')
                        ->label('Assignment Form PDF')
                        ->icon('heroicon-o-document-text')
                        ->url(fn ($record): string => route('employees.assignment-form.pdf', [
                            'employee' => $record,
                        ]))
                        ->openUrlInNewTab(),

                    Action::make('offboarding_checklist')
                        ->label('Offboarding Checklist')
                        ->icon('heroicon-o-clipboard-document-check')
                        ->url(fn ($record): string => route('employees.offboarding.show', [
                            'employee' => $record,
                        ]))
                        ->openUrlInNewTab(),

                    EditAction::make(),
                ])
                    ->label('Actions')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->button(),
            ])
            ->toolbarActions([
                Action::make('import_employees')
                    ->label('Import Employees')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->url(route('employees.import.form'))
                    ->openUrlInNewTab(),

                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}