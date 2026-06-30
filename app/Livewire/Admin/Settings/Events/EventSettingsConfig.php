<?php

namespace App\Livewire\Admin\Settings\Events;

class EventSettingsConfig
{
    /**
     * Secciones disponibles en configuración de eventos y reuniones.
     * Agregar una nueva entrada aquí para escalar el módulo.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function sections(): array
    {
        return [
            'categories' => [
                'key' => 'categories',
                'title' => 'Categorías de eventos',
                'description' => 'Clasificación de eventos y reuniones (periódicos o eventuales), generales o por iglesia.',
                'button_text' => 'Gestionar categorías',
                'route' => 'admin.settings.events.categories',
                'permission' => 'settings.event.category.view',
                'card_bg' => 'bg-primary-50/60 border-primary-100/80',
                'icon_bg' => 'bg-primary-100',
                'icon_c' => 'text-primary-600',
                'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z',
            ],
        ];
    }

    public static function section(string $key): ?array
    {
        return static::sections()[$key] ?? null;
    }

    /**
     * @return array<string, mixed>
     */
    public static function sectionOrFail(string $key): array
    {
        $section = static::section($key);

        if (! $section) {
            abort(404);
        }

        return $section + [
            'index_route' => 'admin.settings.events.index',
        ];
    }
}
