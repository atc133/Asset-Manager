<?php

namespace App\Filament\Resources\MaintenanceCases;

use App\Filament\Resources\MaintenanceCases\Pages\CreateMaintenanceCase;
use App\Filament\Resources\MaintenanceCases\Pages\EditMaintenanceCase;
use App\Filament\Resources\MaintenanceCases\Pages\ListMaintenanceCases;
use App\Filament\Resources\MaintenanceCases\Schemas\MaintenanceCaseForm;
use App\Filament\Resources\MaintenanceCases\Tables\MaintenanceCasesTable;
use App\Models\MaintenanceCase;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MaintenanceCaseResource extends Resource
{
    protected static ?string $model = MaintenanceCase::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

protected static string|\UnitEnum|null $navigationGroup = 'Maintenance & Operations';

protected static ?int $navigationSort = 1;

protected static ?string $modelLabel = 'Maintenance Case';

protected static ?string $pluralModelLabel = 'Maintenance Cases';
    public static function form(Schema $schema): Schema
    {
        return MaintenanceCaseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MaintenanceCasesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function canViewAny(): bool
{
    return auth()->user()?->can('manage_maintenance') ?? false;
}

public static function canCreate(): bool
{
    return auth()->user()?->can('manage_maintenance') ?? false;
}

public static function canEdit($record): bool
{
    return auth()->user()?->can('manage_maintenance') ?? false;
}

public static function canDelete($record): bool
{
    return auth()->user()?->can('manage_maintenance') ?? false;
}

    public static function getPages(): array
    {
        return [
            'index' => ListMaintenanceCases::route('/'),
            'create' => CreateMaintenanceCase::route('/create'),
            'edit' => EditMaintenanceCase::route('/{record}/edit'),
        ];
    }
}
