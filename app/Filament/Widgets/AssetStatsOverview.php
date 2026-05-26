<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AssetStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalAssets = Asset::query()->count();

        $assignedAssets = Asset::query()
            ->where('status', 'assigned')
            ->count();

        $inStorageAssets = Asset::query()
            ->where('status', 'in_storage')
            ->count();

        $inRepairAssets = Asset::query()
            ->where('status', 'in_repair')
            ->count();

        $lostAssets = Asset::query()
            ->where('status', 'lost')
            ->count();

        $missingSerialAssets = Asset::query()
            ->where(function ($query) {
                $query
                    ->whereNull('serial_number')
                    ->orWhere('serial_number', '')
                    ->orWhere('serial_number', '-')
                    ->orWhere('serial_number', '????')
                    ->orWhere('serial_number', 'NO LABEL');
            })
            ->count();

        return [
            Stat::make('Total Assets', $totalAssets)
                ->description('All registered equipment'),

            Stat::make('Assigned', $assignedAssets)
                ->description('Currently assigned to employee or position'),

            Stat::make('In Storage', $inStorageAssets)
                ->description('Available or stored equipment'),

            Stat::make('In Repair', $inRepairAssets)
                ->description('Equipment under repair/check'),

            Stat::make('Lost', $lostAssets)
                ->description('Assets marked as lost'),

            Stat::make('Missing Serial', $missingSerialAssets)
                ->description('Assets without valid serial number'),
        ];
    }
}