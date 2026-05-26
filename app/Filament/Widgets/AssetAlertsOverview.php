<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use App\Models\Employee;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class AssetAlertsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
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

        $repairOver30Days = Asset::query()
            ->where('status', 'in_repair')
            ->where('updated_at', '<=', now()->subDays(30))
            ->count();

        $lostAssets = Asset::query()
            ->where('status', 'lost')
            ->count();

        $damagedAssets = Asset::query()
            ->whereIn('condition', ['damaged', 'broken', 'needs_check'])
            ->count();

        $assignedWithoutHolder = Asset::query()
            ->where('status', 'assigned')
            ->whereNull('current_employee_id')
            ->whereNull('current_position_id')
            ->count();

        $withoutLocation = Asset::query()
            ->whereNull('current_location_id')
            ->count();

        $inactiveEmployeesWithAssets = Employee::query()
            ->where('status', 'inactive')
            ->whereHas('assets')
            ->count();

        return [
            Stat::make('Missing Serial', $missingSerial)
                ->description('Assets without valid serial number')
                ->color($missingSerial > 0 ? 'danger' : 'success'),

            Stat::make('Repair > 30 Days', $repairOver30Days)
                ->description('Assets stuck in repair too long')
                ->color($repairOver30Days > 0 ? 'warning' : 'success'),

            Stat::make('Lost Assets', $lostAssets)
                ->description('Assets marked as lost')
                ->color($lostAssets > 0 ? 'danger' : 'success'),

            Stat::make('Damaged / Check', $damagedAssets)
                ->description('Damaged, broken, or needs check')
                ->color($damagedAssets > 0 ? 'warning' : 'success'),

            Stat::make('Assigned Without Holder', $assignedWithoutHolder)
                ->description('Assigned but no employee or position')
                ->color($assignedWithoutHolder > 0 ? 'danger' : 'success'),

            Stat::make('No Location', $withoutLocation)
                ->description('Assets without current location')
                ->color($withoutLocation > 0 ? 'danger' : 'success'),

            Stat::make('Inactive Employees With Assets', $inactiveEmployeesWithAssets)
                ->description('Offboarding risk')
                ->color($inactiveEmployeesWithAssets > 0 ? 'danger' : 'success'),
        ];
    }
}