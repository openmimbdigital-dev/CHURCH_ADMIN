<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasRoles;
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'email',
        'password',
        'phone_number',
        'status',
        'business_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'status' => 'boolean',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('superAdmin');
    }

    public function isPresbitero(): bool
    {
        return $this->hasRole('Presbitero');
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

        $query->where('business_id', $viewer->business_id);

        $query->whereDoesntHave('roles', fn (Builder $roleQuery) => $roleQuery->where('name', 'superAdmin'));

        if (! $viewer->isPresbitero()) {
            $query->whereDoesntHave('roles', fn (Builder $roleQuery) => $roleQuery->where('name', 'Presbitero'));
        }

        return $query;
    }

    public function isVisibleToAuthUser(?User $viewer = null): bool
    {
        $viewer ??= auth()->user();

        if ($viewer?->isSuperAdmin()) {
            return true;
        }

        if (! $viewer?->business_id || $this->business_id !== $viewer->business_id) {
            return false;
        }

        if ($this->isSuperAdmin()) {
            return false;
        }

        if ($this->isPresbitero() && ! $viewer->isPresbitero()) {
            return false;
        }

        return true;
    }

    public function roleLabelForViewer(?User $viewer = null): ?string
    {
        $viewer ??= auth()->user();
        $role = $this->getRoleNames()->first();

        if (! $role) {
            return null;
        }

        if ($role === 'superAdmin' && ! $viewer?->isSuperAdmin()) {
            return null;
        }

        return $role;
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function administrativeZones(): BelongsToMany
    {
        return $this->belongsToMany(AdministrativeZone::class)
            ->using(AdministrativeZoneUser::class)
            ->withPivot('current_zone')
            ->withTimestamps();
    }

    public function churches(): BelongsToMany
    {
        return $this->belongsToMany(Church::class)
            ->using(ChurchUser::class)
            ->withPivot('current_church')
            ->withTimestamps();
    }

    public function ledAdministrativeZones(): MorphToMany
    {
        return $this->morphedByMany(AdministrativeZone::class, 'leadable', 'leadables')
            ->withTimestamps();
    }

    public function ledChurches(): MorphToMany
    {
        return $this->morphedByMany(Church::class, 'leadable', 'leadables')
            ->withTimestamps();
    }

    public function ledBusinesses(): MorphToMany
    {
        return $this->morphedByMany(Business::class, 'leadable', 'leadables')
            ->withTimestamps();
    }
}
