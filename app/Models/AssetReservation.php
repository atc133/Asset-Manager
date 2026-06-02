<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetReservation extends Model
{
    protected $fillable = [
        'asset_id',
        'employee_id',
        'reserved_by_user_id',
        'reserved_from',
        'reserved_until',
        'status',
        'notes',
    ];

    protected $casts = [
        'reserved_from' => 'date',
        'reserved_until' => 'date',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function reservedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reserved_by_user_id');
    }

protected static function booted(): void
{
    static::saved(function (AssetReservation $reservation): void {
        $asset = $reservation->asset;

        if (! $asset) {
            return;
        }

        if ($reservation->status === 'active') {
            $asset->update([
                'status' => 'reserved',
            ]);

            return;
        }

        if (
            in_array($reservation->status, ['completed', 'cancelled'])
            && $asset->status === 'reserved'
        ) {
            $asset->update([
                'status' => 'available',
            ]);
        }
    });
}
}