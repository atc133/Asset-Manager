<?php

namespace App\Filament\Resources\Employees\Tables;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

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
        ->options(fn () => \App\Models\Employee::query()
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
    \Filament\Actions\Action::make('import_employees')
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