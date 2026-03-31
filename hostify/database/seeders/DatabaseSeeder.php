<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // 1. Roles y permisos primero (Spatie)
            RolesAndPermissionsSeeder::class,

            // 2. Usuarios (depende de roles)
            UserSeeder::class,

            // 3. Catálogos base (sin dependencias entre sí)
            RoomTypeSeeder::class,
            InventoryItemSeeder::class,

            // 4. Habitaciones (depende de room_types)
            RoomSeeder::class,

            // 5. Log inicial de estados (depende de rooms)
            RoomStatusLogSeeder::class,

            // 6. Huéspedes (independiente)
            GuestSeeder::class,

            // 7. Reservas (depende de rooms + guests + users)
            ReservationSeeder::class,

            // 8. Facturas (depende de reservations + guests)
            InvoiceSeeder::class,

            // 9. Cierre de turno (depende de users)
            ShiftCloseSeeder::class,
        ]);
    }
}