<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class MaintenanceCase extends Model
{
    use LogsActivity;

    protected $fillable = [
        'asset_id',
        'handled_by_user_id',
        'issue',
        'description',
        'action_taken',
        'status',
        'opened_at',
        'closed_at',
        'notes',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'asset_id',
                'handled_by_user_id',
                'issue',
                'description',
                'action_taken',
                'status',
                'opened_at',
                'closed_at',
                'notes',
            ])
            ->logOnlyDirty();
            
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function handledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by_user_id');
    }
}