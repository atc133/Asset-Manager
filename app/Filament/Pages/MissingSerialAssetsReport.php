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
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Pages\Concerns\HasReportExports;

class MissingSerialAssetsReport extends Page implements HasTable
{
    use InteractsWithTable;

    use HasReportExports;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    protected static ?string $navigationLabel = 'Missing Serial';

    protected static \UnitEnum|string|null $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.missing-serial-assets-report';

    protected static string $reportKey = 'missing-serial';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Asset::query()
                    ->with(['assetType', 'currentLocation', 'currentEmployee', 'currentPosition'])
                    ->where(function (Builder $query): void {
                        $query
                            ->whereNull('serial_number')
                            ->orWhere('serial_number', '')
                            ->orWhere('serial_number', '-')
                            ->orWhere('serial_number', '????')
                            ->orWhere('serial_number', 'NO LABEL');
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
                    ->placeholder('Missing'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('currentLocation.name')
                    ->label('Location')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('currentEmployee.full_name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('currentPosition.code')
                    ->label('Position')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),
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