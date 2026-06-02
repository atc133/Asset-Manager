<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AssetHealthOverview extends StatsOverviewWidget
{
    protected static ?int $sort = -6;

    protected ?string $heading = 'Asset Health Overview';

    protected function getStats(): array
    {
        $totalAssets = Asset::count();

        $underWarranty = Asset::query()
            ->whereNotNull('warranty_until')
            ->whereDate('warranty_until', '>', now()->addDays(90))
            ->count();

        $warrantyExpiring = Asset::query()
            ->whereNotNull('warranty_until')
            ->whereBetween('warranty_until', [now(), now()->addDays(90)])
            ->count();

        $replacementDue = Asset::query()
            ->whereNotNull('expected_replacement_at')
            ->whereDate('expected_replacement_at', '<=', now())
            ->count();

        $inRepair = Asset::where('status', 'in_repair')->count();

        $damagedOrLost = Asset::query()
            ->whereIn('status', ['damaged', 'lost'])
            ->count();

        $missingSerial = Asset::query()
            ->where(function ($query) {
                $query->whereNull('serial_number')
                    ->orWhere('serial_number', '')
                    ->orWhere('condition', 'missing_serial');
            })
            ->count();

        $critical = $replacementDue + $damagedOrLost;
        $attention = $warrantyExpiring + $inRepair + $missingSerial;

        return [
            Stat::make('Healthy Assets', $underWarranty)
                ->description($totalAssets > 0 ? 'Protected and inside lifecycle' : 'No assets yet')
                ->descriptionIcon('heroicon-m-check-circle')
                ->icon('heroicon-o-shield-check')
                ->color('success'),

            Stat::make('Needs Attention', $attention)
                ->description('Warranty, repair, or data issues')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->icon('heroicon-o-bell-alert')
                ->color($attention > 0 ? 'warning' : 'success'),

            Stat::make('Critical Assets', $critical)
                ->description('Replacement due, damaged, or lost')
                ->descriptionIcon('heroicon-m-fire')
                ->icon('heroicon-o-shield-exclamation')
                ->color($critical > 0 ? 'danger' : 'success'),

            Stat::make('Warranty < 90 Days', $warrantyExpiring)
                ->description('Assets close to warranty expiry')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->icon('heroicon-o-clock')
                ->color($warrantyExpiring > 0 ? 'warning' : 'success'),

            Stat::make('Replacement Due', $replacementDue)
                ->description('Assets past replacement date')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->icon('heroicon-o-arrow-trending-down')
                ->color($replacementDue > 0 ? 'danger' : 'success'),

            Stat::make('Data Quality Issues', $missingSerial)
                ->description('Missing serial or invalid label')
                ->descriptionIcon('heroicon-m-question-mark-circle')
                ->icon('heroicon-o-document-magnifying-glass')
                ->color($missingSerial > 0 ? 'gray' : 'success'),
        ];
    }
}