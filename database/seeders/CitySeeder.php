<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Department;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        if (City::count() > 0) {
            $this->command->info('La tabla cities ya tiene datos. No se insertarán nuevos registros.');

            return;
        }

        $departments = Department::pluck('id', 'code');

        if ($departments->isEmpty()) {
            $this->command->error('No hay departamentos. Ejecuta primero DepartmentSeeder.');

            return;
        }

        $cities = [
            // Amazonas
            ['department_code' => '91', 'name' => 'Leticia', 'code' => 'LET', 'latitude' => 4.2152780, 'longitude' => -69.9405560],
            // Antioquia
            ['department_code' => '05', 'name' => 'Medellín', 'code' => 'MED', 'latitude' => 6.2518400, 'longitude' => -75.5635900],
            ['department_code' => '05', 'name' => 'Bello', 'code' => 'BEL', 'latitude' => 6.3373060, 'longitude' => -75.5579540],
            ['department_code' => '05', 'name' => 'Envigado', 'code' => 'ENV', 'latitude' => 6.1736110, 'longitude' => -75.5916670],
            ['department_code' => '05', 'name' => 'Itagüí', 'code' => 'ITG', 'latitude' => 6.1833330, 'longitude' => -75.6333330],
            // Arauca
            ['department_code' => '81', 'name' => 'Arauca', 'code' => 'AUC', 'latitude' => 7.0847220, 'longitude' => -70.7591670],
            // Atlántico
            ['department_code' => '08', 'name' => 'Barranquilla', 'code' => 'BAQ', 'latitude' => 10.9685400, 'longitude' => -74.7813200],
            ['department_code' => '08', 'name' => 'Soledad', 'code' => 'SOL', 'latitude' => 10.9172220, 'longitude' => -74.7666670],
            // Bogotá D.C.
            ['department_code' => '11', 'name' => 'Bogotá', 'code' => 'BOG', 'latitude' => 4.6097100, 'longitude' => -74.0817500],
            // Bolívar
            ['department_code' => '13', 'name' => 'Cartagena', 'code' => 'CTG', 'latitude' => 10.3997200, 'longitude' => -75.5144400],
            ['department_code' => '13', 'name' => 'Magangué', 'code' => 'MGN', 'latitude' => 9.2419440, 'longitude' => -74.7538890],
            // Boyacá
            ['department_code' => '15', 'name' => 'Tunja', 'code' => 'TUN', 'latitude' => 5.5352800, 'longitude' => -73.3677800],
            ['department_code' => '15', 'name' => 'Duitama', 'code' => 'DUI', 'latitude' => 5.8247220, 'longitude' => -73.0347220],
            // Caldas
            ['department_code' => '17', 'name' => 'Manizales', 'code' => 'MZL', 'latitude' => 5.0688900, 'longitude' => -75.5173800],
            // Caquetá
            ['department_code' => '18', 'name' => 'Florencia', 'code' => 'FLA', 'latitude' => 1.6147220, 'longitude' => -75.6061110],
            // Casanare
            ['department_code' => '85', 'name' => 'Yopal', 'code' => 'YOP', 'latitude' => 5.3377780, 'longitude' => -72.3958330],
            // Cauca
            ['department_code' => '19', 'name' => 'Popayán', 'code' => 'PPN', 'latitude' => 2.4382300, 'longitude' => -76.6131600],
            // Cesar
            ['department_code' => '20', 'name' => 'Valledupar', 'code' => 'VUP', 'latitude' => 10.4631400, 'longitude' => -73.2532200],
            // Chocó
            ['department_code' => '27', 'name' => 'Quibdó', 'code' => 'UIB', 'latitude' => 5.6944440, 'longitude' => -76.6580560],
            // Córdoba
            ['department_code' => '23', 'name' => 'Montería', 'code' => 'MTR', 'latitude' => 8.7479800, 'longitude' => -75.8814300],
            // Cundinamarca
            ['department_code' => '25', 'name' => 'Soacha', 'code' => 'SOA', 'latitude' => 4.5794440, 'longitude' => -74.2169440],
            ['department_code' => '25', 'name' => 'Facatativá', 'code' => 'FAC', 'latitude' => 4.8144440, 'longitude' => -74.3552780],
            ['department_code' => '25', 'name' => 'Girardot', 'code' => 'GIR', 'latitude' => 4.3005560, 'longitude' => -74.8075000],
            ['department_code' => '25', 'name' => 'Zipaquirá', 'code' => 'ZIP', 'latitude' => 5.0222220, 'longitude' => -73.9997220],
            // Guainía
            ['department_code' => '94', 'name' => 'Inírida', 'code' => 'PDA', 'latitude' => 3.8652780, 'longitude' => -67.9238890],
            // Guaviare
            ['department_code' => '95', 'name' => 'San José del Guaviare', 'code' => 'SJG', 'latitude' => 2.5688890, 'longitude' => -72.6416670],
            // Huila
            ['department_code' => '41', 'name' => 'Neiva', 'code' => 'NVA', 'latitude' => 2.9275000, 'longitude' => -75.2877800],
            // La Guajira
            ['department_code' => '44', 'name' => 'Riohacha', 'code' => 'RCH', 'latitude' => 11.5444440, 'longitude' => -72.9072220],
            // Magdalena
            ['department_code' => '47', 'name' => 'Santa Marta', 'code' => 'SMR', 'latitude' => 11.2407900, 'longitude' => -74.2110400],
            // Meta
            ['department_code' => '50', 'name' => 'Villavicencio', 'code' => 'VVC', 'latitude' => 4.1420000, 'longitude' => -73.6266400],
            // Nariño
            ['department_code' => '52', 'name' => 'Pasto', 'code' => 'PSO', 'latitude' => 1.2136100, 'longitude' => -77.2811100],
            // Norte de Santander
            ['department_code' => '54', 'name' => 'Cúcuta', 'code' => 'CUC', 'latitude' => 7.8939100, 'longitude' => -72.5078200],
            // Putumayo
            ['department_code' => '86', 'name' => 'Mocoa', 'code' => 'MOY', 'latitude' => 1.1491670, 'longitude' => -76.6458330],
            // Quindío
            ['department_code' => '63', 'name' => 'Armenia', 'code' => 'AXM', 'latitude' => 4.5338900, 'longitude' => -75.6811100],
            // Risaralda
            ['department_code' => '66', 'name' => 'Pereira', 'code' => 'PEI', 'latitude' => 4.8133300, 'longitude' => -75.6961100],
            // San Andrés y Providencia
            ['department_code' => '88', 'name' => 'San Andrés', 'code' => 'ADZ', 'latitude' => 12.5847220, 'longitude' => -81.7005560],
            // Santander
            ['department_code' => '68', 'name' => 'Bucaramanga', 'code' => 'BGA', 'latitude' => 7.1193500, 'longitude' => -73.1227200],
            ['department_code' => '68', 'name' => 'Floridablanca', 'code' => 'FLB', 'latitude' => 7.0622220, 'longitude' => -73.0863890],
            // Sucre
            ['department_code' => '70', 'name' => 'Sincelejo', 'code' => 'SIN', 'latitude' => 9.3047200, 'longitude' => -75.3977800],
            // Tolima
            ['department_code' => '73', 'name' => 'Ibagué', 'code' => 'IBE', 'latitude' => 4.4388900, 'longitude' => -75.2322200],
            // Valle del Cauca
            ['department_code' => '76', 'name' => 'Cali', 'code' => 'CAL', 'latitude' => 3.4372200, 'longitude' => -76.5225000],
            ['department_code' => '76', 'name' => 'Palmira', 'code' => 'PAL', 'latitude' => 3.5394440, 'longitude' => -76.3036110],
            ['department_code' => '76', 'name' => 'Buenaventura', 'code' => 'BUN', 'latitude' => 3.8805560, 'longitude' => -77.0197220],
            // Vaupés
            ['department_code' => '97', 'name' => 'Mitú', 'code' => 'MIT', 'latitude' => 1.1983330, 'longitude' => -70.1733330],
            // Vichada
            ['department_code' => '99', 'name' => 'Puerto Carreño', 'code' => 'PCR', 'latitude' => 6.1891670, 'longitude' => -67.4858330],
        ];

        foreach ($cities as $city) {
            $departmentId = $departments[$city['department_code']] ?? null;

            if (! $departmentId) {
                continue;
            }

            City::create([
                'department_id' => $departmentId,
                'name' => $city['name'],
                'code' => $city['code'],
                'latitude' => $city['latitude'],
                'longitude' => $city['longitude'],
                'is_active' => true,
            ]);
        }

        $this->command->info('Ciudades de Colombia creadas exitosamente.');
    }
}
