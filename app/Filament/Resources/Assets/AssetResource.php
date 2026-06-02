<?php

namespace App\Filament\Resources\Assets;

use App\Filament\Resources\Assets\Pages\CreateAsset;
use App\Filament\Resources\Assets\Pages\EditAsset;
use App\Filament\Resources\Assets\Pages\ListAssets;
use App\Filament\Resources\Assets\RelationManagers\AssignmentsRelationManager;
use App\Filament\Resources\Assets\RelationManagers\MaintenanceCasesRelationManager;
use App\Filament\Resources\Assets\Schemas\AssetForm;
use App\Filament\Resources\Assets\Tables\AssetsTable;
use App\Models\Asset;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use App\Filament\Resources\Assets\RelationManagers\LifecycleEventsRelationManager;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedComputerDesktop;

    protected static string|\UnitEnum|null $navigationGroup = 'Asset Management';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Asset';

    protected static ?string $pluralModelLabel = 'Assets';

    protected static ?string $recordTitleAttribute = 'asset_tag';

    public static function form(Schema $schema): Schema
    {
        return AssetForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AssetsTable::configure($table);
    }

   public static function getRelations(): array
{
    return [
        AssignmentsRelationManager::class,
        LifecycleEventsRelationManager::class,
        MaintenanceCasesRelationManager::class,
    ];
}

    public static function canViewAny(): bool
{
    return auth()->user()?->can('view_assets') ?? false;
}

public static function canCreate(): bool
{
    return auth()->user()?->can('create_assets') ?? false;
}

public static function canEdit($record): bool
{
    return auth()->user()?->can('edit_assets') ?? false;
}

public static function canDelete($record): bool
{
    return auth()->user()?->can('delete_assets') ?? false;
}

    public static function getPages(): array
    {
        return [
            'index' => ListAssets::route('/'),
            'create' => CreateAsset::route('/create'),
            'edit' => EditAsset::route('/{record}/edit'),
        ];
    }
}