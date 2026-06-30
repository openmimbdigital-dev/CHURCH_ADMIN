<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ChurchEventCategory extends Pivot
{
    protected $table = 'church_event_category';

    public $incrementing = true;

    protected $fillable = [
        'business_id',
        'event_category_id',
        'church_id',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function eventCategory(): BelongsTo
    {
        return $this->belongsTo(EventCategory::class);
    }

    public function church(): BelongsTo
    {
        return $this->belongsTo(Church::class);
    }
}
