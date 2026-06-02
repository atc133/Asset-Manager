<?php

namespace App\Filament\Resources\AssetReservations\Schemas;

use App\Models\Asset;
use App\Models\Employee;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AssetReservationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('asset_id')
                    ->label('Asset')
                    ->options(fn () => Asset::query()
                        ->whereIn('status', ['available', 'in_storage'])
                        ->orderBy('asset_tag')
                        ->get()
                        ->mapWithKeys(fn (Asset $asset) => [
                            $asset->id => $asset->asset_tag . ' - ' . ($asset->assetModel?->name ?? 'Unknown model'),
                        ])
                        ->toArray())
                    ->searchable()
                    ->required(),

                Select::make('employee_id')
                    ->label('Reserved For Employee')
                    ->options(fn () => Employee::query()
                        ->orderBy('full_name')
                        ->pluck('full_name', 'id')
                        ->toArray())
                    ->searchable()
                    ->required(),

                DatePicker::make('reserved_from')
                    ->label('Reserved From')
                    ->native(false)
                    ->default(now()),

                DatePicker::make('reserved_until')
                    ->label('Reserved Until')
                    ->native(false),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('active')
                    ->required(),

                Textarea::make('notes')
                    ->label('Notes')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}