<?php

namespace App\Filament\Resources\Employees\RelationManagers;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class AssignmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'assignments';

    protected static ?string $recordTitleAttribute = 'id';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('location_id')
                    ->relationship('location', 'name')
                    ->required(),

                DatePicker::make('assigned_at')
                    ->required(),

                DatePicker::make('returned_at'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('location.name')
                    ->label('Location'),

                Tables\Columns\TextColumn::make('assigned_at')
                    ->date(),

                Tables\Columns\TextColumn::make('returned_at')
                    ->date()
                    ->placeholder('Still Assigned'),
            ])
            ->headerActions([
    \Filament\Actions\CreateAction::make(),
])
->actions([
    \Filament\Actions\EditAction::make(),
    \Filament\Actions\DeleteAction::make(),
]);
    }
}