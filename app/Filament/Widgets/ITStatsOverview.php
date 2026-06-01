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

    protected ?string $heading = 'IT Asset Overview';

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

        $assignmentRate = $totalAssets > 0
            ? round(($assignedAssets / $totalAssets) * 100)
            : 0;

        $healthyAssets = max($totalAssets - $lostOrDamaged - $missingSerial - $repairAssets, 0);

        return [
            Stat::make('Total Assets', number_format($totalAssets))
                ->description('Complete IT inventory')
                ->descriptionIcon('heroicon-m-archive-box')
                ->icon('heroicon-o-computer-desktop')
                ->color('primary'),

            Stat::make('Assigned Assets', number_format($assignedAssets))
                ->description("{$assignmentRate}% currently deployed")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->icon('heroicon-o-user-circle')
                ->color('success'),

            Stat::make('In Storage', number_format($storageAssets))
                ->description('Ready for assignment')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->icon('heroicon-o-archive-box')
                ->color('info'),

            Stat::make('In Repair', number_format($repairAssets))
                ->description($repairAssets > 0 ? 'Needs technical follow-up' : 'No repair queue')
                ->descriptionIcon($repairAssets > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->icon('heroicon-o-wrench-screwdriver')
                ->color($repairAssets > 0 ? 'warning' : 'success'),

            Stat::make('Missing Serial', number_format($missingSerial))
                ->description($missingSerial > 0 ? 'Data cleanup required' : 'Serial data looks clean')
                ->descriptionIcon($missingSerial > 0 ? 'heroicon-m-exclamation-circle' : 'heroicon-m-check-circle')
                ->icon('heroicon-o-identification')
                ->color($missingSerial > 0 ? 'danger' : 'success'),

            Stat::make('Lost / Damaged', number_format($lostOrDamaged))
                ->description($lostOrDamaged > 0 ? 'Risk items detected' : 'No critical asset condition')
                ->descriptionIcon($lostOrDamaged > 0 ? 'heroicon-m-shield-exclamation' : 'heroicon-m-shield-check')
                ->icon('heroicon-o-shield-exclamation')
                ->color($lostOrDamaged > 0 ? 'danger' : 'success'),

            Stat::make('Open Repair Cases', number_format($openRepairCases))
                ->description($openRepairCases > 0 ? 'Open maintenance workload' : 'No open maintenance cases')
                ->descriptionIcon($openRepairCases > 0 ? 'heroicon-m-clock' : 'heroicon-m-check-circle')
                ->icon('heroicon-o-clipboard-document-list')
                ->color($openRepairCases > 0 ? 'warning' : 'success'),

            Stat::make('Healthy Assets', number_format($healthyAssets))
                ->description('Assets without critical flags')
                ->descriptionIcon('heroicon-m-sparkles')
                ->icon('heroicon-o-heart')
                ->color('primary'),
        ];
    }
}