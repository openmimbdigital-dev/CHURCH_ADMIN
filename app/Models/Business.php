<?php

namespace App\Models;

use App\Models\Concerns\GuardsDeletionWhenReferenced;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Business extends Model
{
    use GuardsDeletionWhenReferenced;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'nit',
        'email',
        'phone_number',
        'address',
        'logo',
        'city_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function administrativeZones(): HasMany
    {
        return $this->hasMany(AdministrativeZone::class);
    }

    public function churches(): HasMany
    {
        return $this->hasMany(Church::class);
    }

    public function churchEventCategories(): HasMany
    {
        return $this->hasMany(ChurchEventCategory::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function leaders(): MorphToMany
    {
        return $this->morphToMany(User::class, 'leadable', 'leadables')
            ->withTimestamps();
    }

    public function scopeVisibleToAuth(Builder $query, ?User $viewer = null): Builder
    {
        $viewer ??= auth()->user();

        if ($viewer?->isSuperAdmin()) {
            return $query;
        }

        return $query->whereRaw('1 = 0');
    }

    public function isVisibleToAuthUser(?User $viewer = null): bool
    {
        return (bool) ($viewer ?? auth()->user())?->isSuperAdmin();
    }

    protected function deletionBlockingRelations(): array
    {
        return [
            'users' => 'usuarios',
            'administrativeZones' => 'zonas administrativas',
            'churches' => 'iglesias',
        ];
    }
}
