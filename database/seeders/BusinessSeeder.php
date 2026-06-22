<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\City;
use App\Models\User;
use Illuminate\Database\Seeder;

class BusinessSeeder extends Seeder
{
    private array $businessOneUsernames = [
        'admin',
        'joselito.ariza',
        'juan.berdugo',
        'carlos.admin',
        'maria.lider',
        'pedro.copastor',
        'ana.asistente',
        'luis.maestro',
        'sofia.coordinadora',
    ];

    public function run(): void
    {
        if (Business::count() < 10) {
            $this->seedBusinesses();
        }

        $this->assignUsersToMainBusiness();

        $this->command->info('Negocios creados exitosamente.');
    }

    private function seedBusinesses(): void
    {
        $medellin = City::where('code', 'MED')->first();
        $bogota = City::where('code', 'BOG')->first();
        $cali = City::where('code', 'CAL')->first();
        $barranquilla = City::where('code', 'BAQ')->first();
        $bucaramanga = City::where('code', 'BGA')->first();

        $businesses = [
            [
                'name' => 'Misión Panamericana de Colombia',
                'slug' => 'mision-panamericana-de-colombia',
                'nit' => '900100001-1',
                'email' => 'contacto@mpanamericana.co',
                'phone_number' => '+57 604 123 4567',
                'address' => 'Calle 50 # 45-23',
                'city_id' => $medellin?->id,
                'leader_username' => 'joselito.ariza',
            ],
            [
                'name' => 'Iglesia Comunidad de Fe Bogotá',
                'slug' => 'iglesia-comunidad-fe-bogota',
                'nit' => '900100002-8',
                'email' => 'info@comunidadfebogota.org',
                'phone_number' => '+57 601 234 5678',
                'address' => 'Carrera 15 # 80-40',
                'city_id' => $bogota?->id,
                'leader_username' => 'diego.pastor',
            ],
            [
                'name' => 'Ministerio Vida Nueva Cali',
                'slug' => 'ministerio-vida-nueva-cali',
                'nit' => '900100003-5',
                'email' => 'hola@vidanuevacali.org',
                'phone_number' => '+57 602 345 6789',
                'address' => 'Av. 6N # 28-30',
                'city_id' => $cali?->id,
                'leader_username' => 'diego.pastor',
            ],
            [
                'name' => 'Centro Cristiano del Caribe',
                'slug' => 'centro-cristiano-caribe',
                'nit' => '900100004-2',
                'email' => 'caribe@cfe.org',
                'phone_number' => '+57 605 456 7890',
                'address' => 'Calle 72 # 54-10',
                'city_id' => $barranquilla?->id,
                'leader_username' => 'diego.pastor',
            ],
            [
                'name' => 'Fraternidad Evangélica Santander',
                'slug' => 'fraternidad-evangelica-santander',
                'nit' => '900100005-9',
                'email' => 'santander@fraternidad.org',
                'phone_number' => '+57 607 567 8901',
                'address' => 'Carrera 27 # 45-12',
                'city_id' => $bucaramanga?->id,
                'leader_username' => 'diego.pastor',
            ],
            [
                'name' => 'Alianza Misionera del Pacífico',
                'slug' => 'alianza-misionera-pacifico',
                'nit' => '900100006-6',
                'email' => 'pacifico@alianza.org',
                'phone_number' => '+57 602 678 9012',
                'address' => 'Calle 5 # 12-45',
                'city_id' => $cali?->id,
                'leader_username' => 'diego.pastor',
            ],
            [
                'name' => 'Red de Iglesias Esperanza',
                'slug' => 'red-iglesias-esperanza',
                'nit' => '900100007-3',
                'email' => 'red@esperanza.org',
                'phone_number' => '+57 601 789 0123',
                'address' => 'Av. Ciudad de Cali # 90-20',
                'city_id' => $bogota?->id,
                'leader_username' => 'diego.pastor',
            ],
            [
                'name' => 'Ministerio Educativo Cristiano',
                'slug' => 'ministerio-educativo-cristiano',
                'nit' => '900100008-0',
                'email' => 'educacion@mec.org',
                'phone_number' => '+57 604 890 1234',
                'address' => 'Carrera 80 # 45-67',
                'city_id' => $medellin?->id,
                'leader_username' => 'diego.pastor',
            ],
            [
                'name' => 'Congregación Fuente de Vida',
                'slug' => 'congregacion-fuente-vida',
                'nit' => '900100009-7',
                'email' => 'fuente@vida.org',
                'phone_number' => '+57 605 901 2345',
                'address' => 'Calle 84 # 72-15',
                'city_id' => $barranquilla?->id,
                'leader_username' => 'diego.pastor',
            ],
            [
                'name' => 'Misión Integral Antioquia',
                'slug' => 'mision-integral-antioquia',
                'nit' => '900100010-4',
                'email' => 'integral@antioquia.org',
                'phone_number' => '+57 604 012 3456',
                'address' => 'Calle 10 # 43-50',
                'city_id' => $medellin?->id,
                'leader_username' => 'diego.pastor',
            ],
        ];

        foreach ($businesses as $data) {
            $leaderUsername = $data['leader_username'];
            unset($data['leader_username']);

            $business = Business::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    ...$data,
                    'is_active' => true,
                ]
            );

            $leader = User::where('username', $leaderUsername)->first();

            if ($leader && ! in_array($leaderUsername, $this->businessOneUsernames, true)) {
                $business->leaders()->syncWithoutDetaching([$leader->id]);
                $leader->update(['business_id' => $business->id]);
            }
        }
    }

    private function assignUsersToMainBusiness(): void
    {
        $mainBusiness = Business::where('slug', 'mision-panamericana-de-colombia')->first();

        if (! $mainBusiness) {
            return;
        }

        $businessOneUsers = User::whereIn('username', $this->businessOneUsernames)->get();

        User::whereIn('username', $this->businessOneUsernames)
            ->update(['business_id' => $mainBusiness->id]);

        $mainBusiness->leaders()->syncWithoutDetaching(
            $businessOneUsers->pluck('id')->all()
        );
    }
}
