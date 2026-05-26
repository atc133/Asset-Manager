<?php

namespace App\Filament\Pages;

use App\Models\Employee;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use UnitEnum;

class HomeOfficeReturns extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?string $navigationLabel = 'Home Office Returns';

    protected static string|UnitEnum|null $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 20;

    protected string $view = 'filament.pages.home-office-returns';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Employee::query()
                    ->with(['assets.assetType'])
                    ->whereIn('work_mode', ['home_office', 'hybrid'])
            )
            ->columns([
                TextColumn::make('full_name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                TextColumn::make('work_mode')
                    ->label('Work Mode')
                    ->badge()
                    ->sortable(),

                IconColumn::make('return_required')
                    ->label('Return Required')
                    ->boolean(),

                TextColumn::make('return_required_at')
                    ->label('Return Date')
                    ->date()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('assets_count')
                    ->label('Assigned Assets')
                    ->counts('assets')
                    ->sortable(),

                TextColumn::make('return_notes')
                    ->label('Notes')
                    ->limit(40)
                    ->placeholder('-'),
            ])
            ->defaultSort('return_required', 'desc');
    }
}