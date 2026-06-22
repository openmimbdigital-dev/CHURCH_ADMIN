<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\MenuSection;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            [
                'name' => 'Usuarios',
                'code' => 'users',
                'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
                'sort_order' => 10,
                'items' => [
                    [
                        'name' => 'Listar usuarios',
                        'code' => 'users.list',
                        'url' => '#',
                        'icon' => 'M4 6h16M4 10h16M4 14h16M4 18h16',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Roles y permisos',
                        'code' => 'users.roles-permissions',
                        'url' => '#',
                        'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                        'sort_order' => 2,
                    ],
                ],
            ],
            [
                'name' => 'Administrar Iglesia',
                'code' => 'church-admin',
                'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                'sort_order' => 20,
                'items' => [
                    [
                        'name' => 'Zonas',
                        'code' => 'church-admin.zones',
                        'url' => '#',
                        'icon' => 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Iglesias',
                        'code' => 'church-admin.churches',
                        'url' => '#',
                        'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                        'sort_order' => 2,
                    ],
                ],
            ],
        ];

        foreach ($sections as $sectionData) {
            $items = $sectionData['items'];
            unset($sectionData['items']);

            $section = MenuSection::updateOrCreate(
                ['code' => $sectionData['code']],
                [
                    ...$sectionData,
                    'is_active' => true,
                ]
            );

            foreach ($items as $itemData) {
                MenuItem::updateOrCreate(
                    [
                        'menu_section_id' => $section->id,
                        'code' => $itemData['code'],
                    ],
                    [
                        ...$itemData,
                        'is_active' => true,
                    ]
                );
            }
        }

        $this->command->info('Menú lateral creado exitosamente.');
    }
}
