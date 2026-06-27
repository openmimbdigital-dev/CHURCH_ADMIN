<?php

namespace App\Models;

use App\Models\Concerns\GuardsDeletionWhenReferenced;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdministrativeZone extends Model
{
    use GuardsDeletionWhenReferenced;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'business_id',
        'name',
        'address',
        'city_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
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

        return $query->where('business_id', $viewer->business_id);
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

        return $this->business_id === $viewer->business_id;
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->using(AdministrativeZoneUser::class)
            ->withTimestamps();
    }

    public function leaders(): MorphToMany
    {
        return $this->morphToMany(User::class, 'leadable', 'leadables')
            ->withTimestamps();
    }

    public function churches(): HasMany
    {
        return $this->hasMany(Church::class);
    }

    /**
     * @return array<string, string>
     */
    protected function deletionBlockingRelations(): array
    {
        return [
            'churches' => 'iglesias',
        ];
    }
}
