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
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class HomeOfficeReturnCaseResource extends Resource
{
    protected static ?string $model = HomeOfficeReturnCase::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUturnLeft;

    protected static ?string $navigationLabel = 'Home Office Returns';

    protected static string|UnitEnum|null $navigationGroup = 'Maintenance & Operations';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Home Office Return';

    protected static ?string $pluralModelLabel = 'Home Office Returns';

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('employee_id')
                ->label('Employee')
                ->relationship('employee', 'full_name')
                ->searchable()
                ->preload()
                ->required(),

            Select::make('status')
                ->label('Status')
                ->options([
                    'open' => 'Open',
                    'contacted' => 'Contacted',
                    'scheduled' => 'Scheduled',
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
                ->relationship('assignedTo', 'name')
                ->searchable()
                ->preload(),

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
                    ->sortable()
                    ->icon('heroicon-m-user-circle'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable()
                    ->color(fn (?string $state): string => match ($state) {
                        'open' => 'danger',
                        'contacted' => 'info',
                        'scheduled' => 'warning',
                        'in_progress' => 'primary',
                        'completed' => 'success',
                        'cancelled' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('priority')
                    ->label('Priority')
                    ->badge()
                    ->sortable()
                    ->color(fn (?string $state): string => match ($state) {
                        'urgent' => 'danger',
                        'high' => 'warning',
                        'normal' => 'primary',
                        'low' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('items_count')
                    ->label('Assets')
                    ->counts('items')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('pending_items_count')
                    ->label('Pending')
                    ->counts('pendingItems')
                    ->badge()
                    ->color(fn ($state): string => ((int) $state) > 0 ? 'warning' : 'success'),

                TextColumn::make('assignedTo.name')
                    ->label('Technician')
                    ->icon('heroicon-m-wrench-screwdriver')
                    ->placeholder('Unassigned'),

                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('-')
                    ->color(fn ($record): string => $record->due_date && $record->due_date->isPast() && ! in_array($record->status, ['completed', 'cancelled'])
                        ? 'danger'
                        : 'gray'),

                TextColumn::make('requested_at')
                    ->label('Requested')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'open' => 'Open',
                        'contacted' => 'Contacted',
                        'scheduled' => 'Scheduled',
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