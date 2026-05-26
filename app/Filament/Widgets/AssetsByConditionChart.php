<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use Filament\Widgets\ChartWidget;

class AssetsByConditionChart extends ChartWidget
{
    protected ?string $maxHeight = '260px';
    protected ?string $heading = 'Assets by Condition';

    protected static ?int $sort = 2;

    protected ?string $pollingInterval = null;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $data = Asset::query()
            ->selectRaw('condition, COUNT(*) as total')
            ->groupBy('condition')
            ->orderBy('condition')
            ->pluck('total', 'condition');

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