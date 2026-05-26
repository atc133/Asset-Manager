<?php

namespace App\Filament\Resources\MaintenanceCases\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MaintenanceCaseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('asset_id')
                    ->label('Asset')
                    ->relationship('asset', 'asset_tag')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->asset_tag} - {$record->brand} {$record->model} - {$record->serial_number}")
                    ->searchable(['asset_tag', 'serial_number', 'brand', 'model'])
                    ->preload()
                    ->required(),

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
}