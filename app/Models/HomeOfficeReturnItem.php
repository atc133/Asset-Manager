<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomeOfficeReturnItem extends Model
{
    protected $fillable = [
        'home_office_return_case_id',
        'asset_id',
        'replacement_asset_id',
        'status',
        'returned_at',
        'condition_on_return',
        'notes',
    ];

    protected $casts = [
        'returned_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (HomeOfficeReturnItem $item): void {
            if (in_array($item->status, ['returned', 'checked']) && blank($item->returned_at)) {
                $item->returned_at = now();
            }
        });

        static::saved(function (HomeOfficeReturnItem $item): void {
            $item->syncAssetAfterReturnStatus();
            $item->syncCaseStatus();
        });
    }

    protected function syncAssetAfterReturnStatus(): void
    {
        $asset = $this->asset;

        if (! $asset) {
            return;
        }

        match ($this->status) {
            'returned', 'checked' => $asset->forceFill([
                'status' => 'in_storage',
                'condition' => $this->condition_on_return ?: $asset->condition,
                'current_employee_id' => null,
                'current_position_id' => null,
            ])->save(),

            'damaged' => $asset->forceFill([
                'status' => 'damaged',
                'condition' => 'damaged',
                'current_employee_id' => null,
                'current_position_id' => null,
            ])->save(),

            'missing' => $asset->forceFill([
                'status' => 'lost',
                'current_employee_id' => null,
                'current_position_id' => null,
            ])->save(),

            default => null,
        };

        if ($this->status === 'replaced' && $this->replacementAsset) {
            $this->replacementAsset->forceFill([
                'status' => 'assigned',
                'current_employee_id' => $this->returnCase?->employee_id,
                'current_position_id' => null,
                'current_location_id' => $this->returnCase?->employee?->default_location_id,
            ])->save();
        }
    }

    protected function syncCaseStatus(): void
    {
        $case = $this->returnCase;

        if (! $case) {
            return;
        }

        $case->refresh();

        $totalItems = $case->items()->count();

        if ($totalItems === 0) {
            return;
        }

        $openItems = $case->items()
            ->whereIn('status', ['pending'])
            ->count();

        if ($openItems === 0) {
            $case->forceFill([
                'status' => 'completed',
                'completed_at' => now(),
            ])->saveQuietly();

            return;
        }

        if ($case->status === 'open') {
            $case->forceFill([
                'status' => 'in_progress',
            ])->saveQuietly();
        }
    }

    public function returnCase(): BelongsTo
    {
        return $this->belongsTo(HomeOfficeReturnCase::class, 'home_office_return_case_id');
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function replacementAsset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'replacement_asset_id');
    }
}