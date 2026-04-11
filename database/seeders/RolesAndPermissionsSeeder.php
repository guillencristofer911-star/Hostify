<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Usuarios
            'view_any_user', 'create_user', 'edit_user', 'delete_user',

            // Tipos de habitación
            'view_any_room_type', 'create_room_type', 'edit_room_type', 'delete_room_type',

            // Habitaciones
            'view_any_room', 'create_room', 'edit_room', 'delete_room',

            // Huéspedes
            'view_any_guest', 'create_guest', 'edit_guest', 'delete_guest',

            // Reservas
            'view_any_reservation', 'create_reservation', 'edit_reservation', 'delete_reservation',

            // Facturas
            'view_any_invoice', 'view_invoice', 'create_invoice', 'edit_invoice', 'delete_invoice',

            // Cierres de turno
            'view_any_shift_close', 'create_shift_close', 'view_shift_close',

            // Sesiones de limpieza ← FALTABAN
            'view_any_cleaning_session', 'create_cleaning_session',
            'edit_cleaning_session', 'delete_cleaning_session',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Super Admin — acceso total
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        // Supervisor
        $supervisor = Role::firstOrCreate(['name' => 'supervisor', 'guard_name' => 'web']);
        $supervisor->syncPermissions([
            'view_any_room_type', 'create_room_type', 'edit_room_type', 'delete_room_type',
            'view_any_room', 'create_room', 'edit_room', 'delete_room',
            'view_any_guest', 'create_guest', 'edit_guest', 'delete_guest',
            'view_any_reservation', 'create_reservation', 'edit_reservation', 'delete_reservation',
            'view_any_invoice', 'view_invoice',
            'view_any_shift_close', 'create_shift_close', 'view_shift_close',
            'view_any_cleaning_session', 'create_cleaning_session', 'edit_cleaning_session',
        ]);

        // Recepcionista
        $recepcionista = Role::firstOrCreate(['name' => 'recepcionista', 'guard_name' => 'web']);
        $recepcionista->syncPermissions([
            'view_any_room_type',
            'view_any_room',
            'view_any_guest', 'create_guest', 'edit_guest',
            'view_any_reservation', 'create_reservation', 'edit_reservation',
            'view_any_invoice', 'view_invoice',
            'view_any_shift_close', 'create_shift_close', 'view_shift_close',
            'view_any_cleaning_session',
        ]);

        // Camarera
        $camarera = Role::firstOrCreate(['name' => 'camarera', 'guard_name' => 'web']);
        $camarera->syncPermissions([
            'view_any_room',
            'view_any_cleaning_session',
        ]);

        $this->command->info(' Roles y permisos sincronizados correctamente.');
    }
}