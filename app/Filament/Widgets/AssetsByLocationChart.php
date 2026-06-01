<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use Filament\Widgets\ChartWidget;

class AssetsByLocationChart extends ChartWidget
{
    protected ?string $maxHeight = '260px';
    protected ?string $heading = 'Assets by Location';

    protected static ?int $sort = 4;
    
    protected ?string $pollingInterval = null;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $data = Asset::query()
            ->leftJoin('locations', 'assets.current_location_id', '=', 'locations.id')
            ->selectRaw('COALESCE(locations.name, "No Location") as location_name, COUNT(*) as total')
            ->groupBy('location_name')
            ->orderBy('location_name')
            ->pluck('total', 'location_name');

        return [
            'datasets' => [
                [
                    'label' => 'Assets',
                    'data' => $data->values()->toArray(),
                    'backgroundColor' => [
    '#3b82f6',
    '#22c55e',
    '#f59e0b',
    '#ef4444',
    '#8b5cf6',
    '#06b6d4',
    '#64748b',
    '#ec4899',
],
'borderColor' => '#ffffff',
                ],
            ],
            'labels' => $data->keys()->toArray(),
        ];
    }
}