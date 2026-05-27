<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Assets\AssetResource;
use App\Models\Asset;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use App\Filament\Pages\Concerns\HasReportExports;

class HomeOfficeAssetsReport extends Page implements HasTable
{
    use InteractsWithTable;

    use HasReportExports;
    
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?string $navigationLabel = 'Home Office Assets';

    protected static \UnitEnum|string|null $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.home-office-assets-report';

    protected static string $reportKey = 'home-office';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Asset::query()
                    ->with(['assetType', 'currentLocation', 'currentEmployee'])
                    ->whereHas('currentLocation', function ($query): void {
                        $query->where('type', 'home_office');
                    })
            )
            ->columns([
                TextColumn::make('asset_tag')
                    ->label('Asset Tag')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('assetType.name')
                    ->label('Type')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('serial_number')
                    ->label('Serial')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('currentEmployee.full_name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('currentLocation.name')
                    ->label('Location')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('condition')
                    ->label('Condition')
                    ->badge()
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('open')
                    ->label('Open')
                    ->url(fn (Asset $record): string => AssetResource::getUrl('edit', [
                        'record' => $record,
                    ])),
            ]);
    }
}