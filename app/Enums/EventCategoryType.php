<?php

namespace App\Enums;

enum EventCategoryType: string
{
    case Culto = 'culto';
    case Educacion = 'educacion';
    case Social = 'social';
    case Administrativo = 'administrativo';
    case Otro = 'otro';

    public function label(): string
    {
        return match ($this) {
            self::Culto => 'Culto',
            self::Educacion => 'Educación',
            self::Social => 'Social',
            self::Administrativo => 'Administrativo',
            self::Otro => 'Otro',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $type) => [$type->value => $type->label()])
            ->all();
    }
}
