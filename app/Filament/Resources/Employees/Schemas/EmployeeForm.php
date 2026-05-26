<?php

namespace App\Filament\Resources\Employees\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EmployeeForm
{
    
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

            \Filament\Forms\Components\Toggle::make('return_required')
    ->label('Equipment Return Required')
    ->helperText('Mark this when the employee must return home office equipment.')
    ->live(),

\Filament\Forms\Components\DatePicker::make('return_required_at')
    ->label('Return Required Date')
    ->visible(fn ($get) => $get('return_required')),

\Filament\Forms\Components\Textarea::make('return_notes')
    ->label('Return Notes')
    ->rows(3)
    ->visible(fn ($get) => $get('return_required'))
    ->columnSpanFull(),
    
                TextInput::make('full_name')
                    ->label('Full Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(255),

                TextInput::make('department')
                    ->label('Department')
                    ->maxLength(255),

                Select::make('work_mode')
                    ->label('Work Mode')
                    ->options([
                        'office' => 'Office',
                        'home_office' => 'Home Office',
                        'hybrid' => 'Hybrid',
                    ])
                    ->required()
                    ->default('office'),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->required()
                    ->default('active'),

                Select::make('default_location_id')
                    ->label('Default Location')
                    ->relationship('defaultLocation', 'name')
                    ->searchable()
                    ->preload(),
            ]);
    }
}