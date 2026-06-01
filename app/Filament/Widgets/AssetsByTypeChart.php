<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use Filament\Widgets\ChartWidget;

class AssetsByTypeChart extends ChartWidget
{
    protected ?string $heading = 'Assets by Type';

    protected static ?int $sort = -7;

    protected  ?string $maxHeight = '260px';

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'md' => 1,
    ];

    protected function getData(): array
    {
        $data = Asset::query()
            ->join('asset_types', 'assets.asset_type_id', '=', 'asset_types.id')
            ->selectRaw('asset_types.name as type_name, COUNT(*) as total')
            ->groupBy('asset_types.name')
            ->orderByDesc('total')
            ->limit(8)
            ->pluck('total', 'type_name');

        return [
            'datasets' => [
                [
                    'label' => 'Assets',
                    'data' => $data->values()->toArray(),
                    'backgroundColor' => [
                        '#408dcb',
                        '#dc3a8d',
                        '#8dd4ed',
                        '#2563eb',
                        '#9333ea',
                        '#06b6d4',
                        '#f59e0b',
                        '#10b981',
                    ],
                ],
            ],
            'labels' => $data->keys()->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}