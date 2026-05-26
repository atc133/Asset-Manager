<?php

namespace App\Imports;

use App\Models\Asset;
use App\Models\AssetModel;
use App\Models\AssetType;
use App\Models\Brand;
use App\Models\Employee;
use App\Models\Location;
use App\Models\Position;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AssetsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $assetType = null;
        $brand = null;
        $assetModel = null;
        $location = null;
        $position = null;
        $employee = null;

        if (! empty($row['type'])) {
            $typeName = trim((string) $row['type']);

            $assetType = AssetType::query()
                ->where('name', $typeName)
                ->orWhere('code', strtoupper($typeName))
                ->first();

            if (! $assetType) {
                $assetType = AssetType::create([
                    'name' => $typeName,
                    'code' => strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $typeName), 0, 10)),
                    'requires_serial' => true,
                    'is_consumable' => false,
                ]);
            }
        }

        if (! empty($row['brand'])) {
            $brand = Brand::firstOrCreate([
                'name' => trim((string) $row['brand']),
            ]);
        }

        if ($brand && ! empty($row['model'])) {
            $assetModel = AssetModel::firstOrCreate([
                'brand_id' => $brand->id,
                'name' => trim((string) $row['model']),
            ]);
        }

        if (! empty($row['location_code'])) {
            $location = Location::where('code', trim((string) $row['location_code']))->first();
        }

        if (! empty($row['location']) && ! $location) {
            $location = Location::where('name', trim((string) $row['location']))->first();
        }

        if (! empty($row['position_code'])) {
            $position = Position::where('code', trim((string) $row['position_code']))->first();
        }

        if (! empty($row['employee_email'])) {
            $employee = Employee::where('email', trim((string) $row['employee_email']))->first();
        }

        $isConsumable = (bool) ($assetType?->is_consumable);

        $assetTag = ! empty($row['asset_tag'])
            ? trim((string) $row['asset_tag'])
            : null;

        if ($isConsumable) {
            $assetTag = null;
        }

        $lookup = $assetTag
            ? ['asset_tag' => $assetTag]
            : [
                'asset_type_id' => $assetType?->id,
                'brand_id' => $brand?->id,
                'asset_model_id' => $assetModel?->id,
                'serial_number' => ! empty($row['serial_number']) ? trim((string) $row['serial_number']) : null,
            ];

        return Asset::updateOrCreate(
            $lookup,
            [
                'asset_tag' => $assetTag,
                'asset_type_id' => $assetType?->id,
                'brand_id' => $brand?->id,
                'asset_model_id' => $assetModel?->id,

                // κρατάμε και τα παλιά πεδία για compatibility με παλιά reports/imports
                'brand' => $brand?->name,
                'model' => $assetModel?->name,

                'serial_number' => ! empty($row['serial_number']) ? trim((string) $row['serial_number']) : null,
                'status' => $row['status'] ?? ($isConsumable ? 'in_storage' : 'available'),
                'condition' => $row['condition'] ?? 'good',
                'current_location_id' => $location?->id,
                'current_position_id' => $position?->id,
                'current_employee_id' => $employee?->id,
                'notes' => $row['notes'] ?? null,
            ]
        );
    }
}