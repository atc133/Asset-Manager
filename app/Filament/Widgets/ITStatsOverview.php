<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use App\Models\MaintenanceCase;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class ITStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = -10;

    protected function getStats(): array
    {
        $totalAssets = Asset::count();

        $assignedAssets = Asset::where('status', 'assigned')->count();

        $storageAssets = Asset::where('status', 'in_storage')->count();

        $repairAssets = Asset::where('status', 'in_repair')->count();

        $missingSerial = Asset::query()
            ->where(function (Builder $query): void {
                $query
                    ->whereNull('serial_number')
                    ->orWhere('serial_number', '')
                    ->orWhere('serial_number', '-')
                    ->orWhere('serial_number', '????')
                    ->orWhere('serial_number', 'NO LABEL');
            })
            ->count();

        $lostOrDamaged = Asset::query()
            ->where(function (Builder $query): void {
                $query
                    ->where('status', 'lost')
                    ->orWhereIn('condition', ['damaged', 'broken', 'needs_check']);
            })
            ->count();

        $openRepairCases = MaintenanceCase::whereIn('status', ['open', 'in_progress'])->count();

        return [
            Stat::make('Total Assets', $totalAssets)
                ->description('All registered equipment')
                ->icon('heroicon-o-computer-desktop')
                ->color('primary'),

            Stat::make('Assigned', $assignedAssets)
                ->description('Currently assigned assets')
                ->icon('heroicon-o-user')
                ->color('success'),

            Stat::make('In Storage', $storageAssets)
                ->description('Available in storage')
                ->icon('heroicon-o-archive-box')
                ->color('gray'),

            Stat::make('In Repair', $repairAssets)
                ->description('Assets currently in repair')
                ->icon('heroicon-o-wrench-screwdriver')
                ->color($repairAssets > 0 ? 'warning' : 'success'),

            Stat::make('Missing Serial', $missingSerial)
                ->description('Assets missing valid serial')
                ->icon('heroicon-o-exclamation-triangle')
                ->color($missingSerial > 0 ? 'danger' : 'success'),

            Stat::make('Lost / Damaged', $lostOrDamaged)
                ->description('Lost, damaged, broken, needs check')
                ->icon('heroicon-o-shield-exclamation')
                ->color($lostOrDamaged > 0 ? 'danger' : 'success'),

            Stat::make('Open Repair Cases', $openRepairCases)
                ->description('Maintenance cases not closed')
                ->icon('heroicon-o-clipboard-document-list')
                ->color($openRepairCases > 0 ? 'warning' : 'success'),
        ];
    }
}