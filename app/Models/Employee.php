<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Employee extends Model
{
    use LogsActivity;

    protected $fillable = [
        'full_name',
        'email',
        'department',
        'work_mode',
        'status',
        'default_location_id',
        'return_required',
'return_required_at',
'return_notes',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'full_name',
                'email',
                'department',
                'work_mode',
                'status',
                'default_location_id',
            ])
            ->logOnlyDirty();
            
    }

    public function defaultLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'default_location_id');
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'current_employee_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(\App\Models\AssetAssignment::class);
    }

    public function homeOfficeReturnCases(): HasMany
{
    return $this->hasMany(HomeOfficeReturnCase::class);
}

    protected function casts(): array
{
    return [
        'return_required' => 'boolean',
        'return_required_at' => 'date',
    ];
}
}