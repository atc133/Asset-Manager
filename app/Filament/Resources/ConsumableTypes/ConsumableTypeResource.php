<?php

namespace App\Filament\Resources\ConsumableTypes;

use App\Filament\Resources\ConsumableTypes\Pages\CreateConsumableType;
use App\Filament\Resources\ConsumableTypes\Pages\EditConsumableType;
use App\Filament\Resources\ConsumableTypes\Pages\ListConsumableTypes;
use App\Filament\Resources\ConsumableTypes\Schemas\ConsumableTypeForm;
use App\Filament\Resources\ConsumableTypes\Tables\ConsumableTypesTable;
use App\Models\ConsumableType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;
use App\Filament\Resources\ConsumableTypes\RelationManagers\TransactionsRelationManager;

class ConsumableTypeResource extends Resource
{
    protected static ?string $model = ConsumableType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static ?string $navigationLabel = 'Consumables';

    protected static string|UnitEnum|null $navigationGroup = 'Asset Management';

    protected static ?int $navigationSort = 6;

    protected static ?string $modelLabel = 'Consumable';

    protected static ?string $pluralModelLabel = 'Consumables';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ConsumableTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ConsumableTypesTable::configure($table);
    }

    public static function getRelations(): array
{
    return [
        TransactionsRelationManager::class,
    ];
}

    public static function getPages(): array
    {
        return [
            'index' => ListConsumableTypes::route('/'),
            'create' => CreateConsumableType::route('/create'),
            'edit' => EditConsumableType::route('/{record}/edit'),
        ];
    }
}