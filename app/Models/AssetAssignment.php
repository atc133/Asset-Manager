<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class AssetAssignment extends Model
{
    use LogsActivity;

    protected $fillable = [
        'asset_id',
        'assignment_type',
        'employee_id',
        'position_id',
        'location_id',
        'assigned_from',
        'assigned_until',
        'status',
        'notes',
    ];

    protected $casts = [
        'assigned_from' => 'datetime',
        'assigned_until' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'asset_id',
                'assignment_type',
                'employee_id',
                'position_id',
                'location_id',
                'assigned_from',
                'assigned_until',
                'status',
                'notes',
            ])
            ->logOnlyDirty();
            
    }

    protected static function booted(): void
    {
        static::saving(function (AssetAssignment $assignment): void {
            if ($assignment->status !== 'active') {
                return;
            }

            static::query()
                ->where('asset_id', $assignment->asset_id)
                ->where('status', 'active')
                ->when($assignment->exists, function ($query) use ($assignment) {
                    $query->where('id', '!=', $assignment->id);
                })
                ->update([
                    'status' => 'completed',
                    'assigned_until' => now(),
                ]);
        });

        static::saved(function (AssetAssignment $assignment): void {
            if ($assignment->status !== 'active') {
                return;
            }

            $asset = $assignment->asset;

            if (! $asset) {
                return;
            }

            $asset->current_employee_id = null;
            $asset->current_position_id = null;
            $asset->current_location_id = null;

            match ($assignment->assignment_type) {
                'employee' => self::applyEmployeeAssignment($asset, $assignment),
                'position' => self::applyPositionAssignment($asset, $assignment),
                'storage' => self::applyStorageAssignment($asset, $assignment),
                'repair' => self::applyRepairAssignment($asset, $assignment),
                'retired' => self::applyRetiredAssignment($asset, $assignment),
                'lost' => self::applyLostAssignment($asset, $assignment),
                default => null,
            };

            $asset->save();
        });
    }

    private static function applyEmployeeAssignment(Asset $asset, AssetAssignment $assignment): void
    {
        $asset->current_employee_id = $assignment->employee_id;
        $asset->current_location_id = $assignment->employee?->default_location_id;
        $asset->status = 'assigned';
    }

    private static function applyPositionAssignment(Asset $asset, AssetAssignment $assignment): void
    {
        $asset->current_position_id = $assignment->position_id;
        $asset->current_location_id = $assignment->position?->location_id;
        $asset->status = 'assigned';
    }

    private static function applyStorageAssignment(Asset $asset, AssetAssignment $assignment): void
    {
        $asset->current_location_id = $assignment->location_id;
        $asset->status = 'in_storage';
    }

    private static function applyRepairAssignment(Asset $asset, AssetAssignment $assignment): void
    {
        $asset->current_location_id = $assignment->location_id;
        $asset->status = 'in_repair';
    }

    private static function applyRetiredAssignment(Asset $asset, AssetAssignment $assignment): void
    {
        $asset->current_location_id = $assignment->location_id;
        $asset->status = 'retired';
    }

    private static function applyLostAssignment(Asset $asset, AssetAssignment $assignment): void
    {
        $asset->current_location_id = $assignment->location_id;
        $asset->status = 'lost';
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}