<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsumableTransaction extends Model
{
    protected $fillable = [
        'consumable_type_id',
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'created_by',
        'notes',
    ];

    protected static function booted(): void
    {
        static::creating(function (ConsumableTransaction $transaction): void {

            $consumable = $transaction->consumableType;

            $before = $consumable->current_stock;

            $after = match ($transaction->type) {
                'stock_in' => $before + $transaction->quantity,
                'stock_out' => $before - $transaction->quantity,
                'adjustment' => $transaction->quantity,
                default => $before,
            };

            $transaction->stock_before = $before;
            $transaction->stock_after = $after;

            $transaction->created_by = auth()->id();
        });

        static::created(function (ConsumableTransaction $transaction): void {

            $transaction->consumableType->update([
                'current_stock' => $transaction->stock_after,
            ]);
        });
    }

    public function consumableType(): BelongsTo
    {
        return $this->belongsTo(ConsumableType::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}