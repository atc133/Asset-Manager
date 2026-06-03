<?php

namespace App\Filament\Resources\ConsumableTypes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ConsumableTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('category')
                    ->label('Category')
                    ->maxLength(255)
                    ->placeholder('Cables, Peripherals, Toners, Accessories'),

                TextInput::make('minimum_stock')
                    ->label('Minimum Stock')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->required(),

                TextInput::make('current_stock')
                    ->label('Current Stock')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->disabled()
                    ->dehydrated(false)
                    ->helperText('Stock is updated through Stock In / Stock Out actions.'),

                Toggle::make('active')
                    ->label('Active')
                    ->default(true),

                Textarea::make('description')
                    ->label('Description')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}