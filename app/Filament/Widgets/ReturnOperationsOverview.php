<?php

namespace App\Filament\Widgets;

use App\Models\HomeOfficeReturnCase;
use App\Models\HomeOfficeReturnItem;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ReturnOperationsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = -7;

    protected ?string $heading = 'IT Return Operations';

    protected function getStats(): array
    {
        $openCases = HomeOfficeReturnCase::query()
            ->whereIn('status', ['open', 'contacted', 'scheduled', 'in_progress'])
            ->count();

        $overdueCases = HomeOfficeReturnCase::query()
            ->whereIn('status', ['open', 'contacted', 'scheduled', 'in_progress'])
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', now())
            ->count();

        $pendingAssets = HomeOfficeReturnItem::query()
            ->where('status', 'pending')
            ->count();

        $completedThisMonth = HomeOfficeReturnCase::query()
            ->where('status', 'completed')
            ->whereMonth('completed_at', now()->month)
            ->whereYear('completed_at', now()->year)
            ->count();

        $criticalCases = HomeOfficeReturnCase::query()
            ->whereIn('status', ['open', 'contacted', 'scheduled', 'in_progress'])
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<=', now()->subDays(14))
            ->count();

        return [
            Stat::make('Open Returns', $openCases)
                ->description('Active return cases')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color($openCases > 0 ? 'primary' : 'success'),

            Stat::make('Pending Assets', $pendingAssets)
                ->description('Assets waiting to be returned')
                ->descriptionIcon('heroicon-m-clock')
                ->icon('heroicon-o-computer-desktop')
                ->color($pendingAssets > 0 ? 'warning' : 'success'),

            Stat::make('Overdue Cases', $overdueCases)
                ->description('Past due date')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->icon('heroicon-o-calendar-days')
                ->color($overdueCases > 0 ? 'danger' : 'success'),

            Stat::make('Critical Cases', $criticalCases)
                ->description('14+ days overdue')
                ->descriptionIcon('heroicon-m-fire')
                ->icon('heroicon-o-shield-exclamation')
                ->color($criticalCases > 0 ? 'danger' : 'success'),

            Stat::make('Completed This Month', $completedThisMonth)
                ->description('Closed return cases')
                ->descriptionIcon('heroicon-m-check-circle')
                ->icon('heroicon-o-check-badge')
                ->color('success'),
        ];
    }
}