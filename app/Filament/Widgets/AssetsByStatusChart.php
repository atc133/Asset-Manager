<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use Filament\Widgets\ChartWidget;

class AssetsByStatusChart extends ChartWidget
{
    protected ?string $maxHeight = '260px';
    protected ?string $heading = 'Assets by Status';

    protected static ?int $sort = 1;

    protected ?string $pollingInterval = null;

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $data = Asset::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->orderBy('status')
            ->pluck('total', 'status');

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