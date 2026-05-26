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