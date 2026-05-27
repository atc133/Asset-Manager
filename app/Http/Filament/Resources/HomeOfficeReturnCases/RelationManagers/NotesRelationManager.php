<?php

namespace App\Filament\Resources\HomeOfficeReturnCases\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NotesRelationManager extends RelationManager
{
    protected static string $relationship = 'notes';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('contact_type')
                ->label('Contact Type')
                ->options([
                    'note' => 'Note',
                    'phone' => 'Phone',
                    'email' => 'Email',
                    'sms' => 'SMS',
                    'in_person' => 'In Person',
                    'other' => 'Other',
                ])
                ->required()
                ->default('note'),

            Textarea::make('note')
                ->label('Note')
                ->rows(5)
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('contact_type')
                    ->label('Type')
                    ->badge(),

                TextColumn::make('note')
                    ->label('Note')
                    ->limit(80),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime(),

                TextColumn::make('user.name')
                    ->label('User')
                    ->placeholder('-'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateDataUsing(function (array $data): array {
                        $data['user_id'] = auth()->id();

                        return $data;
                    }),
            ]);
    }
}