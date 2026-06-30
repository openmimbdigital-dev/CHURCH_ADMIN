<?php

namespace App\Enums;

enum EventCategoryType: string
{
    case Periodico = 'periodico';
    case Eventual = 'eventual';

    public function label(): string
    {
        return match ($this) {
            self::Periodico => 'Periódico',
            self::Eventual => 'Eventual',
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
