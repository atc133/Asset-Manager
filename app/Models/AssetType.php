<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetType extends Model
{
    protected $fillable = [
        'name',
        'code',
        'requires_serial',
        'is_consumable',
    ];

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }
}