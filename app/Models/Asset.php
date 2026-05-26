<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Asset extends Model
{
    use LogsActivity;

    protected $fillable = [
        'asset_tag',
        'asset_type_id',
        'brand_id',
        'asset_model_id',
        'brand',
        'model',
        'serial_number',
        'status',
        'condition',
        'current_location_id',
        'current_position_id',
        'current_employee_id',
        'notes',
    ];

protected static function booted(): void
{
    static::creating(function (Asset $asset): void {
        $assetType = AssetType::find($asset->asset_type_id);

        if ($assetType?->is_consumable) {
            $asset->asset_tag = null;

            return;
        }

        if (filled($asset->asset_tag)) {
            return;
        }

        $asset->asset_tag = static::generateAssetTag(
            assetTypeId: $asset->asset_type_id,
        );
    });
}

public static function generateAssetTag(int $assetTypeId): string
{
    $assetType = AssetType::findOrFail($assetTypeId);

    $typeCode = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $assetType->code ?: $assetType->name));

    $prefix = $typeCode;

    $lastAsset = static::query()
        ->where('asset_tag', 'like', "{$prefix}-%")
        ->orderByDesc('asset_tag')
        ->first();

    $nextNumber = 1;

    if ($lastAsset) {
        $lastNumber = (int) str($lastAsset->asset_tag)->afterLast('-')->toString();
        $nextNumber = $lastNumber + 1;
    }

    return "{$prefix}-" . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
}

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'asset_tag',
                'asset_type_id',
                'brand_id',
                'asset_model_id',
                'brand',
                'model',
                'serial_number',
                'status',
                'condition',
                'current_location_id',
                'current_position_id',
                'current_employee_id',
                'notes',
            ])
            ->logOnlyDirty();
    }

    public function assetType(): BelongsTo
    {
        return $this->belongsTo(AssetType::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function assetModel(): BelongsTo
    {
        return $this->belongsTo(AssetModel::class);
    }

    public function currentLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'current_location_id');
    }

    public function currentPosition(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'current_position_id');
    }

    public function currentEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'current_employee_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class);
    }

    public function activeAssignment()
    {
        return $this->hasOne(AssetAssignment::class)->where('status', 'active');
    }

    public function maintenanceCases(): HasMany
    {
        return $this->hasMany(MaintenanceCase::class);
    }

    public function homeOfficeReturnItems(): HasMany
    {
        return $this->hasMany(HomeOfficeReturnItem::class);
    }
}