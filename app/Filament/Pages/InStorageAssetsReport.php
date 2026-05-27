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

class InStorageAssetsReport extends Page implements HasTable
{
    use InteractsWithTable;

    use HasReportExports;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static ?string $navigationLabel = 'In Storage';

    protected static \UnitEnum|string|null $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 2;
    protected static string $reportKey = 'storage';

    protected string $view = 'filament.pages.in-storage-assets-report';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Asset::query()
                    ->with(['assetType', 'currentLocation'])
                    ->where('status', 'in_storage')
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

                TextColumn::make('brand')
                    ->label('Brand')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('model')
                    ->label('Model')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('serial_number')
                    ->label('Serial')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('currentLocation.name')
                    ->label('Storage')
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