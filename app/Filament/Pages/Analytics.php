<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AssetsByConditionChart;
use App\Filament\Widgets\AssetsByLocationChart;
use App\Filament\Widgets\AssetsByStatusChart;
use App\Filament\Widgets\AssetsByTypeChart;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class Analytics extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $navigationLabel = 'Analytics';

    protected static string|UnitEnum|null $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 100;

    protected string $view = 'filament.pages.analytics';

    public function getWidgets(): array
    {
        return [
            AssetsByStatusChart::class,
            AssetsByConditionChart::class,
            AssetsByTypeChart::class,
            AssetsByLocationChart::class,
        ];
    }
}