<?php

namespace App\Filament\Resources\Assets\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AssignmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'assignments';

    protected static ?string $title = 'Assignment History';

    public ?string $assignmentFilter = null;

    public ?string $searchEmployee = null;

    public ?string $searchPosition = null;

    public ?string $searchLocation = null;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query): Builder {

                if ($this->searchEmployee) {
                    $query->whereHas('employee', function (Builder $query): void {
                        $query->where('full_name', 'like', '%' . $this->searchEmployee . '%');
                    });
                }

                if ($this->searchPosition) {
                    $query->whereHas('position', function (Builder $query): void {
                        $query->where('code', 'like', '%' . $this->searchPosition . '%');
                    });
                }

                if ($this->searchLocation) {
                    $query->whereHas('location', function (Builder $query): void {
                        $query->where('name', 'like', '%' . $this->searchLocation . '%');
                    });
                }

                return match ($this->assignmentFilter) {
                    'active' => $query->where('status', 'active'),
                    'completed' => $query->where('status', 'completed'),
                    'employee' => $query->where('assignment_type', 'employee'),
                    'position' => $query->where('assignment_type', 'position'),
                    'storage' => $query->where('assignment_type', 'storage'),
                    'repair' => $query->where('assignment_type', 'repair'),
                    'lost' => $query->where('assignment_type', 'lost'),
                    default => $query,
                };
            })
            ->recordTitleAttribute('assignment_type')
            ->headerActions([
                Action::make('all')
                    ->label('All')
                    ->color(fn (): string => $this->assignmentFilter === null ? 'primary' : 'gray')
                    ->action(fn () => $this->assignmentFilter = null),

                Action::make('active')
                    ->label('Active')
                    ->color(fn (): string => $this->assignmentFilter === 'active' ? 'primary' : 'gray')
                    ->action(fn () => $this->assignmentFilter = 'active'),

                Action::make('completed')
                    ->label('Completed')
                    ->color(fn (): string => $this->assignmentFilter === 'completed' ? 'primary' : 'gray')
                    ->action(fn () => $this->assignmentFilter = 'completed'),

                Action::make('employee')
                    ->label('Employee')
                    ->color(fn (): string => $this->assignmentFilter === 'employee' ? 'primary' : 'gray')
                    ->action(fn () => $this->assignmentFilter = 'employee'),

                Action::make('position')
                    ->label('Position')
                    ->color(fn (): string => $this->assignmentFilter === 'position' ? 'primary' : 'gray')
                    ->action(fn () => $this->assignmentFilter = 'position'),

                Action::make('storage')
                    ->label('Storage')
                    ->color(fn (): string => $this->assignmentFilter === 'storage' ? 'primary' : 'gray')
                    ->action(fn () => $this->assignmentFilter = 'storage'),

                Action::make('repair')
                    ->label('Repair')
                    ->color(fn (): string => $this->assignmentFilter === 'repair' ? 'primary' : 'gray')
                    ->action(fn () => $this->assignmentFilter = 'repair'),
            ])
          
            ->columns([
                TextColumn::make('assignment_type')
                    ->label('Type')
                    ->badge()
                    ->sortable(),

                TextColumn::make('employee.full_name')
                    ->label('Employee')
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('position.code')
                    ->label('Position')
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('location.name')
                    ->label('Location')
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('assigned_from')
                    ->label('From')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('assigned_until')
                    ->label('Until')
                    ->dateTime()
                    ->placeholder('-')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(40)
                    ->toggleable(),
            ])
            ->contentFooter([
                TextInput::make('searchEmployee')
                    ->label('Search Employee')
                    ->live(),

                TextInput::make('searchPosition')
                    ->label('Search Position')
                    ->live(),

                TextInput::make('searchLocation')
                    ->label('Search Location')
                    ->live(),
            ])
            ->defaultSort('assigned_from', 'desc')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}