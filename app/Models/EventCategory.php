<?php

namespace App\Models;

use App\Enums\EventCategoryType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventCategory extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'business_id',
        'church_id',
        'name',
        'type',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'type' => EventCategoryType::class,
            'active' => 'boolean',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function church(): BelongsTo
    {
        return $this->belongsTo(Church::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
