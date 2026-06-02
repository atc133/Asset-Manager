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

    protected static function booted(): void
    {
        static::saved(function (Employee $employee): void {
            if (! $employee->wasChanged('status')) {
                return;
            }

            if ($employee->status !== 'inactive') {
                return;
            }

            $employee->createOffboardingReturnCaseIfNeeded();
        });
    }

    public function createOffboardingReturnCaseIfNeeded(): ?HomeOfficeReturnCase
    {
        $assignedAssets = $this->assets()
            ->where('status', 'assigned')
            ->get();

        if ($assignedAssets->isEmpty()) {
            return null;
        }

        $existingOpenCase = $this->homeOfficeReturnCases()
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->first();

        if ($existingOpenCase) {
            return $existingOpenCase;
        }

        $case = $this->homeOfficeReturnCases()->create([
            'requested_by_user_id' => auth()->id(),
            'assigned_to_user_id' => auth()->id(),
            'status' => 'open',
            'priority' => 'high',
            'requested_at' => now(),
            'due_date' => now()->addDays(7)->toDateString(),
            'summary_notes' => trim(
                'Auto-created because employee was marked inactive.' . PHP_EOL .
                ($this->return_notes ?: '')
            ),
        ]);

        foreach ($assignedAssets as $asset) {
            $case->items()->create([
                'asset_id' => $asset->id,
                'status' => 'pending',
                'notes' => 'Auto-added from employee offboarding.',
            ]);
        }

        $this->forceFill([
            'return_required' => true,
            'return_required_at' => now()->toDateString(),
        ])->saveQuietly();

        return $case;
    }

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
                'return_required',
                'return_required_at',
                'return_notes',
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
        return $this->hasMany(AssetAssignment::class);
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

    public function reservations(): HasMany
{
    return $this->hasMany(AssetReservation::class);
}
}