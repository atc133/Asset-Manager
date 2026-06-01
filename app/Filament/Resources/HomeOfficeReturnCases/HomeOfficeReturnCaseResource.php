<?php

namespace App\Filament\Resources\HomeOfficeReturnCases;

use App\Filament\Resources\HomeOfficeReturnCases\Pages\CreateHomeOfficeReturnCase;
use App\Filament\Resources\HomeOfficeReturnCases\Pages\EditHomeOfficeReturnCase;
use App\Filament\Resources\HomeOfficeReturnCases\Pages\ListHomeOfficeReturnCases;
use App\Filament\Resources\HomeOfficeReturnCases\RelationManagers\ItemsRelationManager;
use App\Filament\Resources\HomeOfficeReturnCases\RelationManagers\NotesRelationManager;
use App\Models\Employee;
use App\Models\HomeOfficeReturnCase;
use App\Models\User;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class HomeOfficeReturnCaseResource extends Resource
{
    protected static ?string $model = HomeOfficeReturnCase::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

protected static string|\UnitEnum|null $navigationGroup = 'Maintenance & Operations';

protected static ?int $navigationSort = 1;

protected static ?string $modelLabel = 'Maintenance Case';

protected static ?string $pluralModelLabel = 'Maintenance Cases';

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('employee_id')
                ->label('Employee')
                ->options(fn () => Employee::query()
                    ->orderBy('full_name')
                    ->pluck('full_name', 'id')
                    ->toArray())
                ->searchable()
                ->required(),

            Select::make('status')
                ->label('Status')
                ->options([
                    'open' => 'Open',
                    'in_progress' => 'In Progress',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled',
                ])
                ->required()
                ->default('open'),

            Select::make('priority')
                ->label('Priority')
                ->options([
                    'low' => 'Low',
                    'normal' => 'Normal',
                    'high' => 'High',
                    'urgent' => 'Urgent',
                ])
                ->required()
                ->default('normal'),

            Select::make('assigned_to_user_id')
                ->label('Assigned IT User')
                ->options(fn () => User::query()
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->toArray())
                ->searchable(),

            DatePicker::make('due_date')
                ->label('Due Date'),

            Textarea::make('summary_notes')
                ->label('Summary Notes')
                ->rows(4)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                TextColumn::make('employee.full_name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('priority')
                    ->label('Priority')
                    ->badge()
                    ->sortable(),

                TextColumn::make('items_count')
                    ->label('Assets')
                    ->counts('items'),

                TextColumn::make('pending_items_count')
                    ->label('Pending')
                    ->counts('pendingItems'),

                TextColumn::make('assignedTo.name')
                    ->label('Assigned IT')
                    ->placeholder('-'),

                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('requested_at')
                    ->label('Requested')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('-'),
            ])
            ->filters([
    SelectFilter::make('status')
        ->label('Status')
        ->options([
            'open' => 'Open',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ]),

    SelectFilter::make('priority')
        ->label('Priority')
        ->options([
            'low' => 'Low',
            'normal' => 'Normal',
            'high' => 'High',
            'urgent' => 'Urgent',
        ]),

    SelectFilter::make('employee_id')
        ->label('Employee')
        ->relationship('employee', 'full_name')
        ->searchable()
        ->preload(),

    SelectFilter::make('assigned_to_user_id')
        ->label('Assigned IT User')
        ->relationship('assignedTo', 'name')
        ->searchable()
        ->preload(),

    Filter::make('overdue')
        ->label('Overdue')
        ->query(fn (Builder $query): Builder => $query
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', now())),
])
->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
            NotesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHomeOfficeReturnCases::route('/'),
            'create' => CreateHomeOfficeReturnCase::route('/create'),
            'edit' => EditHomeOfficeReturnCase::route('/{record}/edit'),
        ];
    }
}