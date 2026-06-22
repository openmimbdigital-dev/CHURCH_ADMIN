<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
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
