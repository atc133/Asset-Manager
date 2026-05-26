<?php

namespace App\Filament\Resources\Assets\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MaintenanceCasesRelationManager extends RelationManager
{
    protected static string $relationship = 'maintenanceCases';

    protected static ?string $title = 'Maintenance History';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('issue')
                    ->label('Issue')
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->label('Description')
                    ->rows(3)
                    ->columnSpanFull(),

                Select::make('handled_by_user_id')
                    ->label('Handled By')
                    ->relationship('handledBy', 'name')
                    ->searchable()
                    ->preload(),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'open' => 'Open',
                        'in_progress' => 'In Progress',
                        'fixed' => 'Fixed',
                        'cannot_fix' => 'Cannot Fix',
                        'retired' => 'Retired',
                    ])
                    ->required()
                    ->default('open'),

                Textarea::make('action_taken')
                    ->label('Action Taken')
                    ->rows(3)
                    ->columnSpanFull(),

                DateTimePicker::make('opened_at')
                    ->label('Opened At')
                    ->seconds(false)
                    ->default(now()),

                DateTimePicker::make('closed_at')
                    ->label('Closed At')
                    ->seconds(false),

                Textarea::make('notes')
                    ->label('Notes')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('issue')
            ->columns([
                TextColumn::make('issue')
                    ->label('Issue')
                    ->searchable()
                    ->sortable()
                    ->limit(35),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('handledBy.name')
                    ->label('Handled By')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('opened_at')
                    ->label('Opened')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('closed_at')
                    ->label('Closed')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('action_taken')
                    ->label('Action Taken')
                    ->limit(40)
                    ->toggleable(),

                TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'open' => 'Open',
                        'in_progress' => 'In Progress',
                        'fixed' => 'Fixed',
                        'cannot_fix' => 'Cannot Fix',
                        'retired' => 'Retired',
                    ]),
            ])
            ->defaultSort('opened_at', 'desc')
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}