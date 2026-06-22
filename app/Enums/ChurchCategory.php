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
}
