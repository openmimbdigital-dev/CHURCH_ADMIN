<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AdministrativeZoneUser extends Pivot
{
    protected $table = 'administrative_zone_user';

    public $incrementing = true;

    protected $fillable = [
        'user_id',
        'administrative_zone_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function administrativeZone(): BelongsTo
    {
        return $this->belongsTo(AdministrativeZone::class);
    }
}
