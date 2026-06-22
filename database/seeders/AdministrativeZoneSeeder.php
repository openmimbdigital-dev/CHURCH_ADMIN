<?php

namespace Database\Seeders;

use App\Models\AdministrativeZone;
use App\Models\Business;
use App\Models\City;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdministrativeZoneSeeder extends Seeder
{
    public function run(): void
    {
        if (AdministrativeZone::count() >= 10) {
            $this->command->info('La tabla administrative_zones ya tiene datos suficientes.');

            return;
        }

        $businesses = Business::orderBy('id')->get();

        if ($businesses->isEmpty()) {
            $this->command->error('No hay negocios. Ejecuta primero BusinessSeeder.');

            return;
        }

        $cities = City::whereIn('code', ['MED', 'BOG', 'CAL', 'BAQ', 'BGA', 'CTG', 'CUC', 'IBE', 'PEI', 'SMR'])
            ->get()
            ->keyBy('code');

        $zones = [
            ['business_slug' => 'mision-panamericana-de-colombia', 'name' => 'Zona Metropolitana Antioquia', 'city_code' => 'MED', 'leader' => 'joselito.ariza'],
            ['business_slug' => 'mision-panamericana-de-colombia', 'name' => 'Zona Costa Atlántica', 'city_code' => 'BAQ', 'leader' => 'juan.berdugo'],
            ['business_slug' => 'iglesia-comunidad-fe-bogota', 'name' => 'Zona Central Bogotá', 'city_code' => 'BOG', 'leader' => 'carlos.admin'],
            ['business_slug' => 'ministerio-vida-nueva-cali', 'name' => 'Zona Sur Occidente', 'city_code' => 'CAL', 'leader' => 'diego.pastor'],
            ['business_slug' => 'centro-cristiano-caribe', 'name' => 'Zona Norte Caribe', 'city_code' => 'BAQ', 'leader' => 'maria.lider'],
            ['business_slug' => 'fraternidad-evangelica-santander', 'name' => 'Zona Santander', 'city_code' => 'BGA', 'leader' => 'pedro.copastor'],
            ['business_slug' => 'alianza-misionera-pacifico', 'name' => 'Zona Pacífico', 'city_code' => 'CAL', 'leader' => 'ana.asistente'],
            ['business_slug' => 'red-iglesias-esperanza', 'name' => 'Zona Cundinamarca', 'city_code' => 'BOG', 'leader' => 'luis.maestro'],
            ['business_slug' => 'ministerio-educativo-cristiano', 'name' => 'Zona Educativa Medellín', 'city_code' => 'MED', 'leader' => 'sofia.coordinadora'],
            ['business_slug' => 'congregacion-fuente-vida', 'name' => 'Zona Magdalena', 'city_code' => 'SMR', 'leader' => 'admin'],
        ];

        foreach ($zones as $data) {
            $business = $businesses->firstWhere('slug', $data['business_slug']);

            if (! $business) {
                continue;
            }

            $zone = AdministrativeZone::updateOrCreate(
                [
                    'business_id' => $business->id,
                    'name' => $data['name'],
                ],
                [
                    'address' => 'Sede '.$data['name'],
                    'city_id' => $cities[$data['city_code']]?->id,
                    'is_active' => true,
                ]
            );

            $leader = User::where('username', $data['leader'])->first();

            if ($leader) {
                $zone->leaders()->syncWithoutDetaching([$leader->id]);
            }
        }

        $this->command->info('Zonas administrativas creadas exitosamente.');
    }
}
