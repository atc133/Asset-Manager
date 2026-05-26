<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HomeOfficeReturnCase extends Model
{
    protected $fillable = [
        'employee_id',
        'requested_by_user_id',
        'assigned_to_user_id',
        'status',
        'priority',
        'requested_at',
        'due_date',
        'completed_at',
        'summary_notes',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'due_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(HomeOfficeReturnItem::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(HomeOfficeReturnNote::class);
    }

    public function pendingItems(): HasMany
    {
        return $this->items()->where('status', 'pending');
    }
}