<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomeOfficeReturnNote extends Model
{
    protected $fillable = [
        'home_office_return_case_id',
        'user_id',
        'contact_type',
        'note',
    ];

    public function returnCase(): BelongsTo
    {
        return $this->belongsTo(HomeOfficeReturnCase::class, 'home_office_return_case_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}