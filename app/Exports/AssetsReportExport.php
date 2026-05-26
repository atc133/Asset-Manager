<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AssetsReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(
        protected Collection $assets
    ) {}

    public function collection(): Collection
    {
        return $this->assets;
    }

    public function headings(): array
    {
        return [
            'Asset Tag',
            'Type',
            'Brand',
            'Model',
            'Serial',
            'Status',
            'Condition',
            'Location',
            'Employee',
            'Position',
        ];
    }

    public function map($asset): array
    {
        return [
            $asset->asset_tag,
            $asset->assetType?->name,
            $asset->brand,
            $asset->model,
            $asset->serial_number,
            $asset->status,
            $asset->condition,
            $asset->currentLocation?->name,
            $asset->currentEmployee?->full_name,
            $asset->currentPosition?->code,
        ];
    }
}