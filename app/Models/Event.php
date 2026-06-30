<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'business_id',
        'church_id',
        'event_category_id',
        'name',
        'date',
        'start_time',
        'end_time',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'active' => 'boolean',
        ];
    }

    public function scopeVisibleToAuth(Builder $query, ?User $viewer = null): Builder
    {
        $viewer ??= auth()->user();

        if ($viewer?->isSuperAdmin()) {
            return $query;
        }

        if (! $viewer?->business_id || ! $viewer->current_church_id) {
            return $query->whereRaw('1 = 0');
        }

        return $query
            ->where('business_id', $viewer->business_id)
            ->where('church_id', $viewer->current_church_id);
    }

    public function isVisibleToAuthUser(?User $viewer = null): bool
    {
        $viewer ??= auth()->user();

        if ($viewer?->isSuperAdmin()) {
            return true;
        }

        if (! $viewer?->business_id || ! $viewer->current_church_id) {
            return false;
        }

        return $this->business_id === $viewer->business_id
            && (int) $this->church_id === (int) $viewer->current_church_id;
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function church(): BelongsTo
    {
        return $this->belongsTo(Church::class);
    }

    public function eventCategory(): BelongsTo
    {
        return $this->belongsTo(EventCategory::class);
    }
}
