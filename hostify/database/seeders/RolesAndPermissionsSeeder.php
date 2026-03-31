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
        // Limpiar caché de permisos antes de crear
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Permisos por recurso ──────────────────────────────────────────
        // Nomenclatura: accion_recurso  (snake_case, consistente con Filament)
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

            // Facturas (solo lectura para recepcionista y supervisor)
            'view_any_invoice', 'view_invoice',

            // Cierres de turno
            'view_any_shift_close', 'create_shift_close', 'view_shift_close',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // ── Roles y asignación de permisos ───────────────────────────────

        // Super Admin — acceso total sin restricciones
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        // Supervisor — todo excepto gestión de usuarios
        $supervisor = Role::firstOrCreate(['name' => 'supervisor', 'guard_name' => 'web']);
        $supervisor->syncPermissions([
            'view_any_room_type', 'create_room_type', 'edit_room_type', 'delete_room_type',
            'view_any_room', 'create_room', 'edit_room', 'delete_room',
            'view_any_guest', 'create_guest', 'edit_guest', 'delete_guest',
            'view_any_reservation', 'create_reservation', 'edit_reservation', 'delete_reservation',
            'view_any_invoice', 'view_invoice',
            'view_any_shift_close', 'create_shift_close', 'view_shift_close',
        ]);

        // Recepcionista — operaciones diarias, sin configuración ni usuarios
        $recepcionista = Role::firstOrCreate(['name' => 'recepcionista', 'guard_name' => 'web']);
        $recepcionista->syncPermissions([
            'view_any_room_type',
            'view_any_room',
            'view_any_guest', 'create_guest', 'edit_guest',
            'view_any_reservation', 'create_reservation', 'edit_reservation',
            'view_any_invoice', 'view_invoice',
            'view_any_shift_close', 'create_shift_close', 'view_shift_close',
        ]);

        // Camarera — acceso solo a habitaciones (panel), sin panel Filament completo
        // Sus permisos Filament son mínimos porque operará desde vista Blade dedicada
        $camarera = Role::firstOrCreate(['name' => 'camarera', 'guard_name' => 'web']);
        $camarera->syncPermissions([
            'view_any_room',
        ]);

        $this->command->info(' Roles y permisos sincronizados correctamente.');
    }
}