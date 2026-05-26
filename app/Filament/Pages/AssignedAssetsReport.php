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

class AssignedAssetsReport extends Page implements HasTable
{
    use InteractsWithTable;

    use HasReportExports;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel = 'Assigned Assets';

   protected static \UnitEnum|string|null $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.assigned-assets-report';
    protected static string $reportKey = 'assigned';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Asset::query()
                    ->with(['assetType', 'currentLocation', 'currentEmployee', 'currentPosition'])
                    ->where('status', 'assigned')
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

                TextColumn::make('currentPosition.code')
                    ->label('Position')
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