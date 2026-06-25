<?php

namespace App\Enums;

enum ChurchCategory: string
{
    case Principal = 'principal';
    case CampoBlanco = 'campo blanco';
    case Hija = 'hija';

    public function label(): string
    {
        return match ($this) {
            self::Principal => 'Principal',
            self::CampoBlanco => 'Campo blanco',
            self::Hija => 'Hija',
        };
    }

    public function requiresMotherChurch(): bool
    {
        return in_array($this, [self::Hija, self::CampoBlanco], true);
    }

    public static function requiresMotherChurchValue(?string $value): bool
    {
        $category = $value ? self::tryFrom($value) : null;

        return $category?->requiresMotherChurch() ?? false;
    }
}
