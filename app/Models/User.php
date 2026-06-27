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
use Illuminate\Support\Facades\DB;
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
        'current_church_id',
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

        if (! $viewer->current_church_id) {
            return $query->whereRaw('1 = 0');
        }

        $query->whereHas('churches', fn (Builder $churchQuery) => $churchQuery->where(
            'churches.id',
            $viewer->current_church_id
        ));

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

        if (! $viewer->current_church_id) {
            return false;
        }

        return $this->belongsToChurch((int) $viewer->current_church_id);
    }

    public function belongsToChurch(int $churchId): bool
    {
        return $this->churches()->whereKey($churchId)->exists();
    }

    /**
     * @return array<int>
     */
    public function churchZoneIds(): array
    {
        $zoneIds = $this->churches()
            ->pluck('administrative_zone_id')
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        if ($zoneIds->isEmpty() && $this->current_church_id) {
            $zoneId = Church::query()
                ->whereKey($this->current_church_id)
                ->value('administrative_zone_id');

            if ($zoneId) {
                $zoneIds->push((int) $zoneId);
            }
        }

        return $zoneIds->unique()->values()->all();
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

    public function currentChurch(): BelongsTo
    {
        return $this->belongsTo(Church::class, 'current_church_id');
    }

    public function syncCurrentChurchFromPivot(): void
    {
        $churchId = DB::table('church_user')
            ->where('user_id', $this->id)
            ->whereNotNull('current_church')
            ->value('current_church');

        if ($churchId && (int) $this->current_church_id !== (int) $churchId) {
            $this->update(['current_church_id' => $churchId]);
        }
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
