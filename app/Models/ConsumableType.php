<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConsumableType extends Model
{
    protected $fillable = [
        'name',
        'category',
        'minimum_stock',
        'current_stock',
        'active',
        'description',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(ConsumableTransaction::class);
    }

    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->minimum_stock;
    }
}