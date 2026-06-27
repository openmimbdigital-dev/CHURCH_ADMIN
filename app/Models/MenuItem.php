<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_section_id',
        'name',
        'code',
        'url',
        'permission',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(MenuSection::class, 'menu_section_id');
    }

    public function requiredPermission(): ?string
    {
        return $this->permission ?? config("menu-permissions.{$this->code}");
    }

    public function isVisibleTo(?User $user = null): bool
    {
        $user ??= auth()->user();
        $permission = $this->requiredPermission();

        if (! $permission || ! $user) {
            return false;
        }

        return $user->can($permission);
    }
}
