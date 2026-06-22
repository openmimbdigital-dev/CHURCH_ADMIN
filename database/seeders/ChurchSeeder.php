<?php

namespace Database\Seeders;

use App\Enums\ChurchCategory;
use App\Models\AdministrativeZone;
use App\Models\Business;
use App\Models\Church;
use App\Models\City;
use App\Models\User;
use Illuminate\Database\Seeder;

class ChurchSeeder extends Seeder
{
    public function run(): void
    {
        if (Church::count() >= 10) {
            $this->command->info('La tabla churches ya tiene datos suficientes.');

            return;
        }

        $business = Business::where('slug', 'mision-panamericana-de-colombia')->first();
        $zoneCosta = AdministrativeZone::where('name', 'Zona Costa Atlántica')->first();
        $zoneMetro = AdministrativeZone::where('name', 'Zona Metropolitana Antioquia')->first();

        if (! $business) {
            $this->command->error('No se encontró el negocio principal. Ejecuta BusinessSeeder.');

            return;
        }

        $cities = City::whereIn('code', ['BAQ', 'MED', 'BOG', 'CAL', 'BGA', 'CTG', 'CUC', 'IBE', 'PEI', 'SMR'])
            ->get()
            ->keyBy('code');

        $zones = AdministrativeZone::where('business_id', $business->id)->get();
        $fallbackZone = $zoneCosta ?? $zoneMetro ?? $zones->first();

        $churches = [
            [
                'business_slug' => 'mision-panamericana-de-colombia',
                'zone_name' => 'Zona Costa Atlántica',
                'name' => 'Centro de Fe y Esperanza',
                'city_code' => 'BAQ',
                'category' => ChurchCategory::Principal,
                'parent_name' => null,
                'leader' => 'juan.berdugo',
            ],
            [
                'business_slug' => 'mision-panamericana-de-colombia',
                'zone_name' => 'Zona Metropolitana Antioquia',
                'name' => 'Iglesia Sede Medellín',
                'city_code' => 'MED',
                'category' => ChurchCategory::Principal,
                'parent_name' => null,
                'leader' => 'joselito.ariza',
            ],
            [
                'business_slug' => 'mision-panamericana-de-colombia',
                'zone_name' => 'Zona Costa Atlántica',
                'name' => 'Congregación Fe Norte',
                'city_code' => 'BAQ',
                'category' => ChurchCategory::Hija,
                'parent_name' => 'Centro de Fe y Esperanza',
                'leader' => 'maria.lider',
            ],
            [
                'business_slug' => 'mision-panamericana-de-colombia',
                'zone_name' => 'Zona Costa Atlántica',
                'name' => 'Campo Misionero Soledad',
                'city_code' => 'BAQ',
                'category' => ChurchCategory::CampoBlanco,
                'parent_name' => 'Centro de Fe y Esperanza',
                'leader' => 'pedro.copastor',
            ],
            [
                'business_slug' => 'iglesia-comunidad-fe-bogota',
                'zone_name' => 'Zona Central Bogotá',
                'name' => 'Comunidad Fe Chapinero',
                'city_code' => 'BOG',
                'category' => ChurchCategory::Principal,
                'parent_name' => null,
                'leader' => 'carlos.admin',
            ],
            [
                'business_slug' => 'ministerio-vida-nueva-cali',
                'zone_name' => 'Zona Sur Occidente',
                'name' => 'Vida Nueva Granada',
                'city_code' => 'CAL',
                'category' => ChurchCategory::Principal,
                'parent_name' => null,
                'leader' => 'diego.pastor',
            ],
            [
                'business_slug' => 'centro-cristiano-caribe',
                'zone_name' => 'Zona Norte Caribe',
                'name' => 'Cristo Vive Cartagena',
                'city_code' => 'CTG',
                'category' => ChurchCategory::Principal,
                'parent_name' => null,
                'leader' => 'ana.asistente',
            ],
            [
                'business_slug' => 'fraternidad-evangelica-santander',
                'zone_name' => 'Zona Santander',
                'name' => 'Fraternidad Centro',
                'city_code' => 'BGA',
                'category' => ChurchCategory::Principal,
                'parent_name' => null,
                'leader' => 'luis.maestro',
            ],
            [
                'business_slug' => 'ministerio-educativo-cristiano',
                'zone_name' => 'Zona Educativa Medellín',
                'name' => 'Capilla Educativa Bello',
                'city_code' => 'MED',
                'category' => ChurchCategory::Hija,
                'parent_name' => 'Iglesia Sede Medellín',
                'leader' => 'sofia.coordinadora',
            ],
            [
                'business_slug' => 'congregacion-fuente-vida',
                'zone_name' => 'Zona Magdalena',
                'name' => 'Fuente de Vida Santa Marta',
                'city_code' => 'SMR',
                'category' => ChurchCategory::Principal,
                'parent_name' => null,
                'leader' => 'admin',
            ],
        ];

        $createdChurches = [];

        foreach ($churches as $data) {
            $churchBusiness = Business::where('slug', $data['business_slug'])->first()
                ?? $business;

            $zone = AdministrativeZone::where('business_id', $churchBusiness->id)
                ->where('name', $data['zone_name'])
                ->first() ?? $fallbackZone;

            if (! $zone) {
                continue;
            }

            $parentId = null;
            if ($data['parent_name']) {
                $parentId = $createdChurches[$data['parent_name']]
                    ?? Church::where('name', $data['parent_name'])->value('id');
            }

            $church = Church::updateOrCreate(
                [
                    'business_id' => $churchBusiness->id,
                    'name' => $data['name'],
                ],
                [
                    'administrative_zone_id' => $zone->id,
                    'parent_id' => $parentId,
                    'address' => 'Av. Principal # 100-20',
                    'city_id' => $cities[$data['city_code']]?->id,
                    'category' => $data['category'],
                    'is_active' => true,
                ]
            );

            $createdChurches[$data['name']] = $church->id;

            $leader = User::where('username', $data['leader'])->first();

            if ($leader) {
                $church->leaders()->syncWithoutDetaching([$leader->id]);
                if (! $leader->business_id) {
                    $leader->update(['business_id' => $churchBusiness->id]);
                }
            }
        }

        $this->command->info('Iglesias creadas exitosamente.');
    }
}
