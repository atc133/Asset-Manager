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

class InRepairAssetsReport extends Page implements HasTable
{
    use InteractsWithTable;

    use HasReportExports;
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static ?string $navigationLabel = 'In Repair';

    protected static \UnitEnum|string|null $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 3;
    protected static string $reportKey = 'repair';

    protected string $view = 'filament.pages.in-repair-assets-report';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Asset::query()
                    ->with(['assetType', 'currentLocation'])
                    ->where('status', 'in_repair')
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

                TextColumn::make('currentLocation.name')
                    ->label('Repair Location')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('condition')
                    ->label('Condition')
                    ->badge()
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
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