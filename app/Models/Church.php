<?php

namespace App\Models;

use App\Enums\ChurchCategory;
use App\Models\Concerns\GuardsDeletionWhenReferenced;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Church extends Model
{
    use GuardsDeletionWhenReferenced;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'business_id',
        'administrative_zone_id',
        'parent_id',
        'name',
        'address',
        'city_id',
        'category',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'category' => ChurchCategory::class,
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

    public function administrativeZone(): BelongsTo
    {
        return $this->belongsTo(AdministrativeZone::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function leaders(): MorphToMany
    {
        return $this->morphToMany(User::class, 'leadable', 'leadables')
            ->withTimestamps();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Church::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Church::class, 'parent_id');
    }

    public function isPrincipal(): bool
    {
        return $this->category === ChurchCategory::Principal;
    }

    public function isCampoBlanco(): bool
    {
        return $this->category === ChurchCategory::CampoBlanco;
    }

    public function isHija(): bool
    {
        return $this->category === ChurchCategory::Hija;
    }

    /**
     * @return array<string, string>
     */
    protected function deletionBlockingRelations(): array
    {
        return [
            'children' => 'iglesias hijas',
        ];
    }
}
