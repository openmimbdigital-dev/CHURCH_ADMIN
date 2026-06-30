<?php

namespace App\Models;

use App\Enums\EventCategoryType;
use App\Models\Concerns\GuardsDeletionWhenReferenced;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventCategory extends Model
{
    use GuardsDeletionWhenReferenced;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'active',
        'general',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'type' => EventCategoryType::class,
            'active' => 'boolean',
            'general' => 'boolean',
        ];
    }

    public function scopeVisibleToAuth(Builder $query, ?User $viewer = null): Builder
    {
        $viewer ??= auth()->user();

        if ($viewer?->isSuperAdmin()) {
            return $query;
        }

        if (! $viewer?->business_id) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function (Builder $q) use ($viewer) {
            $q->where('general', true)
                ->orWhereIn('id', function ($subQuery) use ($viewer) {
                    $subQuery->select('event_category_id')
                        ->from('church_event_category')
                        ->where('business_id', $viewer->business_id);

                    if ($viewer->can('churches.viewAll')) {
                        return;
                    }

                    if ($viewer->current_church_id) {
                        $subQuery->where('church_id', $viewer->current_church_id);
                    } else {
                        $subQuery->whereRaw('1 = 0');
                    }
                });
        });
    }

    public function isVisibleToAuthUser(?User $viewer = null): bool
    {
        $viewer ??= auth()->user();

        if ($viewer?->isSuperAdmin()) {
            return true;
        }

        if (! $viewer?->business_id) {
            return false;
        }

        if ($this->general) {
            return true;
        }

        $pivotQuery = ChurchEventCategory::query()
            ->where('event_category_id', $this->id)
            ->where('business_id', $viewer->business_id);

        if ($viewer->can('churches.viewAll')) {
            return $pivotQuery->exists();
        }

        if (! $viewer->current_church_id) {
            return false;
        }

        return $pivotQuery
            ->where('church_id', $viewer->current_church_id)
            ->exists();
    }

    public function isManagedByAuthUser(?User $viewer = null): bool
    {
        $viewer ??= auth()->user();

        if (! $this->isVisibleToAuthUser($viewer)) {
            return false;
        }

        if ($this->general) {
            return (bool) $viewer?->isSuperAdmin();
        }

        return true;
    }

    public function churches(): BelongsToMany
    {
        return $this->belongsToMany(Church::class)
            ->using(ChurchEventCategory::class)
            ->withPivot('business_id')
            ->withTimestamps();
    }

    public function churchEventCategories(): HasMany
    {
        return $this->hasMany(ChurchEventCategory::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    protected function deletionBlockingRelations(): array
    {
        return [
            'events' => 'eventos',
            'churches' => 'iglesias asignadas',
        ];
    }
}
