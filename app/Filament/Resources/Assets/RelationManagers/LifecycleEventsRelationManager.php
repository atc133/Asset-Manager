<?php

namespace App\Filament\Resources\Assets\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LifecycleEventsRelationManager extends RelationManager
{
    protected static string $relationship = 'lifecycleEvents';

    protected static ?string $title = 'Lifecycle Timeline';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('event_type')
                    ->label('Event')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => str($state)->replace('_', ' ')->title()->toString())
                    ->color(fn (?string $state): string => match ($state) {
                        'created' => 'success',
                        'employee' => 'primary',
                        'position' => 'info',
                        'storage' => 'gray',
                        'repair' => 'warning',
                        'retired' => 'gray',
                        'lost' => 'danger',
                        default => 'primary',
                    }),

                TextColumn::make('description')
                    ->label('Description')
                    ->wrap()
                    ->searchable(),

                TextColumn::make('employee.full_name')
                    ->label('Employee')
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('position.code')
                    ->label('Position')
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('location.name')
                    ->label('Location')
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('createdBy.name')
                    ->label('User')
                    ->placeholder('System')
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}