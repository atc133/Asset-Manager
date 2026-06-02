<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AssetHealthOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [

            Stat::make(
                'Warranty < 90 Days',
                Asset::query()
                    ->whereNotNull('warranty_until')
                    ->whereBetween('warranty_until', [
                        now(),
                        now()->addDays(90),
                    ])
                    ->count()
            )
                ->color('warning'),

            Stat::make(
                'Replacement Due',
                Asset::query()
                    ->whereNotNull('expected_replacement_at')
                    ->whereDate(
                        'expected_replacement_at',
                        '<=',
                        now()
                    )
                    ->count()
            )
                ->color('danger'),

            Stat::make(
                'In Repair',
                Asset::where('status', 'in_repair')->count()
            )
                ->color('warning'),

            Stat::make(
                'Damaged',
                Asset::where('status', 'damaged')->count()
            )
                ->color('danger'),

            Stat::make(
                'Lost',
                Asset::where('status', 'lost')->count()
            )
                ->color('danger'),

            Stat::make(
                'Missing Serial',
                Asset::query()
                    ->where(function ($q) {
                        $q->whereNull('serial_number')
                          ->orWhere('serial_number', '')
                          ->orWhere('condition', 'missing_serial');
                    })
                    ->count()
            )
                ->color('gray'),
        ];
    }
}