<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use Filament\Widgets\ChartWidget;

class AssetsByStatusChart extends ChartWidget
{
    protected ?string $heading = 'Assets by Status';

    protected static ?int $sort = -6;

    protected ?string $maxHeight = '260px';

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'md' => 1,
        'xl' => 1,
    ];

    protected function getData(): array
    {
        $data = Asset::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->orderByDesc('total')
            ->pluck('total', 'status');

        return [
            'datasets' => [
                [
                    'label' => 'Assets',
                    'data' => $data->values()->toArray(),
                    'backgroundColor' => [
                        '#408dcb',
                        '#dc3a8d',
                        '#8dd4ed',
                        '#10b981',
                        '#f59e0b',
                        '#ef4444',
                        '#64748b',
                    ],
                ],
            ],
            'labels' => $data->keys()
                ->map(fn ($status) => str($status)->replace('_', ' ')->title()->toString())
                ->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}