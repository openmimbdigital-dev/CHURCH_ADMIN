<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ChurchUser extends Pivot
{
    protected $table = 'church_user';

    public $incrementing = true;

    protected $fillable = [
        'user_id',
        'church_id',
        'current_church',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function church(): BelongsTo
    {
        return $this->belongsTo(Church::class);
    }
}
