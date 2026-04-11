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
        // Garantizar que los roles existen antes de asignarlos
        // (por si este seeder se ejecuta de forma aislada)
        if (Role::count() === 0) {
            $this->call(RolesAndPermissionsSeeder::class);
        }

        $users = [
            [
                'name'      => 'Admin Hostify',
                'email'     => 'admin@hostify.com',
                'password'  => Hash::make('hostify2026'),
                'phone'     => '3001234567',
                'is_active' => true,
                'role'      => 'Super Admin',
            ],
            [
                'name'      => 'Ana Recepcionista',
                'email'     => 'ana@hostify.com',
                'password'  => Hash::make('hostify2026'),
                'phone'     => '3009876543',
                'is_active' => true,
                'role'      => 'recepcionista',
            ],
            [
                'name'      => 'María Camarera',
                'email'     => 'maria@hostify.com',
                'password'  => Hash::make('hostify2026'),
                'phone'     => '3005551234',
                'is_active' => true,
                'role'      => 'camarera',
            ],
            [
                'name'      => 'Carlos Supervisor',
                'email'     => 'carlos@hostify.com',
                'password'  => Hash::make('hostify2026'),
                'phone'     => '3007774321',
                'is_active' => true,
                'role'      => 'supervisor',
            ],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);

            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            $user->syncRoles($role); // syncRoles en lugar de assignRole — idempotente
        }

        $this->command->info(' 4 usuarios creados y roles asignados.');
    }
}