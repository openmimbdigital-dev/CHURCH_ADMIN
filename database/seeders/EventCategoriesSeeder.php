<?php

namespace Database\Seeders;

use App\Enums\EventCategoryType;
use App\Models\Church;
use App\Models\ChurchEventCategory;
use App\Models\EventCategory;
use Illuminate\Database\Seeder;

class EventCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedGeneralCategories();
        $this->seedChurchSpecificCategories();

        $this->command->info('Categorías de eventos creadas exitosamente.');
    }

    protected function seedGeneralCategories(): void
    {
        $categories = [
            ['name' => 'Reunion', 'type' => EventCategoryType::Periodico],
            ['name' => 'Campamento', 'type' => EventCategoryType::Eventual],
            ['name' => 'Congreso', 'type' => EventCategoryType::Eventual],
            ['name' => 'Culto', 'type' => EventCategoryType::Periodico],
        ];

        foreach ($categories as $category) {
            EventCategory::withTrashed()->updateOrCreate(
                [
                    'name' => $category['name'],
                    'general' => true,
                ],
                [
                    'type' => $category['type'],
                    'active' => true,
                    'general' => true,
                    'description' => 'Categoría general disponible para todos los negocios e iglesias.',
                ]
            )->restore();
        }
    }

    protected function seedChurchSpecificCategories(): void
    {
        $specificCategories = [
            [
                'church_name' => 'Centro de Fe y Esperanza',
                'name' => 'Escuela dominical',
                'type' => EventCategoryType::Periodico,
                'description' => 'Categoría exclusiva de la iglesia Centro de Fe y Esperanza.',
            ],
            [
                'church_name' => 'Iglesia Sede Medellín',
                'name' => 'Reunión de jóvenes',
                'type' => EventCategoryType::Periodico,
                'description' => 'Categoría exclusiva de la iglesia Sede Medellín.',
            ],
            [
                'church_name' => 'Comunidad Fe Chapinero',
                'name' => 'Grupo de oración',
                'type' => EventCategoryType::Eventual,
                'description' => 'Categoría exclusiva de la iglesia Comunidad Fe Chapinero.',
            ],
            [
                'church_name' => 'Congregación Fe Norte',
                'name' => 'Consejo de líderes',
                'type' => EventCategoryType::Periodico,
                'description' => 'Categoría exclusiva de la iglesia Congregación Fe Norte.',
            ],
        ];

        foreach ($specificCategories as $category) {
            $church = Church::query()
                ->where('name', $category['church_name'])
                ->where('is_active', true)
                ->first(['id', 'business_id', 'name']);

            if (! $church) {
                $this->command->warn("Iglesia no encontrada para categoría: {$category['name']} ({$category['church_name']}).");

                continue;
            }

            $eventCategory = EventCategory::withTrashed()->updateOrCreate(
                [
                    'name' => $category['name'],
                    'general' => false,
                ],
                [
                    'type' => $category['type'],
                    'active' => true,
                    'general' => false,
                    'description' => $category['description'],
                ]
            );

            $eventCategory->restore();

            ChurchEventCategory::query()->updateOrCreate(
                [
                    'event_category_id' => $eventCategory->id,
                    'church_id' => $church->id,
                ],
                [
                    'business_id' => $church->business_id,
                ]
            );
        }
    }
}
