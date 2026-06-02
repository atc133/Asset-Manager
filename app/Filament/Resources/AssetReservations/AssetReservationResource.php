<?php

namespace App\Filament\Resources\AssetReservations;

use App\Filament\Resources\AssetReservations\Pages\CreateAssetReservation;
use App\Filament\Resources\AssetReservations\Pages\EditAssetReservation;
use App\Filament\Resources\AssetReservations\Pages\ListAssetReservations;
use App\Filament\Resources\AssetReservations\Schemas\AssetReservationForm;
use App\Filament\Resources\AssetReservations\Tables\AssetReservationsTable;
use App\Models\AssetReservation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AssetReservationResource extends Resource
{
    protected static ?string $model = AssetReservation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookmarkSquare;

    protected static ?string $navigationLabel = 'Asset Reservations';

    protected static string|UnitEnum|null $navigationGroup = 'Asset Management';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'Asset Reservation';

    protected static ?string $pluralModelLabel = 'Asset Reservations';

    public static function form(Schema $schema): Schema
    {
        return AssetReservationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AssetReservationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAssetReservations::route('/'),
            'create' => CreateAssetReservation::route('/create'),
            'edit' => EditAssetReservation::route('/{record}/edit'),
        ];
    }
}