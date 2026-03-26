<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        //  Crear Roles
        $roles = ['Super Admin', 'recepcionista', 'camarera', 'supervisor'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // Usuarios iniciales
        $users = [
            [
                'name'     => 'Admin HotelX',
                'email'    => 'admin@hotelx.com',
                'password' => Hash::make('hotelx2026'),
                'phone'    => '3001234567',
                'is_active'=> true,
                'role'     => 'Super Admin',
            ],
            [
                'name'     => 'Ana Recepcionista',
                'email'    => 'ana@hotelx.com',
                'password' => Hash::make('hotelx2026'),
                'phone'    => '3009876543',
                'is_active'=> true,
                'role'     => 'recepcionista',
            ],
            [
                'name'     => 'María Camarera',
                'email'    => 'maria@hotelx.com',
                'password' => Hash::make('hotelx2026'),
                'phone'    => '3005551234',
                'is_active'=> true,
                'role'     => 'camarera',
            ],
            [
                'name'     => 'Carlos Supervisor',
                'email'    => 'carlos@hotelx.com',
                'password' => Hash::make('hotelx2026'),
                'phone'    => '3007774321',
                'is_active'=> true,
                'role'     => 'supervisor',
            ],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);

            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            $user->assignRole($role);
        }

        $this->command->info('4 usuarios creados con roles.');
    }
}
