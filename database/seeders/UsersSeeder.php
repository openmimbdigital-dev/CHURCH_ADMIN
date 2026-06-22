<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('Admin12345*');

        $users = [
            ['username' => 'admin', 'first_name' => 'Administrador', 'last_name' => 'Sistema', 'email' => 'admin@church.local', 'role' => 'superAdmin'],
            ['username' => 'joselito.ariza', 'first_name' => 'Joselito', 'last_name' => 'Ariza', 'email' => 'joselito.ariza@church.local', 'role' => 'Presbitero'],
            ['username' => 'juan.berdugo', 'first_name' => 'Juan', 'last_name' => 'Berdugo', 'email' => 'juan.berdugo@church.local', 'role' => 'Pastor'],
            ['username' => 'carlos.admin', 'first_name' => 'Carlos', 'last_name' => 'Mendoza', 'email' => 'carlos.admin@church.local', 'role' => 'Administrador'],
            ['username' => 'maria.lider', 'first_name' => 'María', 'last_name' => 'Gómez', 'email' => 'maria.lider@church.local', 'role' => 'Lider'],
            ['username' => 'pedro.copastor', 'first_name' => 'Pedro', 'last_name' => 'Ramírez', 'email' => 'pedro.copastor@church.local', 'role' => 'Co-pastor'],
            ['username' => 'ana.asistente', 'first_name' => 'Ana', 'last_name' => 'Torres', 'email' => 'ana.asistente@church.local', 'role' => 'Asistente'],
            ['username' => 'luis.maestro', 'first_name' => 'Luis', 'last_name' => 'Vargas', 'email' => 'luis.maestro@church.local', 'role' => 'Maestro'],
            ['username' => 'sofia.coordinadora', 'first_name' => 'Sofía', 'last_name' => 'Castillo', 'email' => 'sofia.coordinadora@church.local', 'role' => 'Coordinador educativo'],
            ['username' => 'diego.pastor', 'first_name' => 'Diego', 'last_name' => 'Herrera', 'email' => 'diego.pastor@church.local', 'role' => 'Pastor'],
        ];

        $rows = [];

        foreach ($users as $data) {
            $user = User::updateOrCreate(
                ['username' => $data['username']],
                [
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'email' => $data['email'],
                    'password' => $password,
                    'phone_number' => null,
                    'status' => true,
                ]
            );

            $user->syncRoles([$data['role']]);

            $rows[] = [$data['username'], $data['role'], 'Admin12345*'];
        }

        $this->command->table(['Usuario', 'Rol', 'Contraseña'], $rows);
    }
}
