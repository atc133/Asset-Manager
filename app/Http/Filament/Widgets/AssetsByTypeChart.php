<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use Filament\Widgets\ChartWidget;

class AssetsByTypeChart extends ChartWidget
{
    protected ?string $maxHeight = '260px';
    protected ?string $heading = 'Assets by Type';

    protected static ?int $sort = 3;

    protected ?string $pollingInterval = null;
    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $data = Asset::query()
            ->join('asset_types', 'assets.asset_type_id', '=', 'asset_types.id')
            ->selectRaw('asset_types.name as type_name, COUNT(*) as total')
            ->groupBy('asset_types.name')
            ->orderBy('asset_types.name')
            ->pluck('total', 'type_name');

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