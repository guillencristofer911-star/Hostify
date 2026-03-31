<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class, //  Primero siempre — permisos y roles
            UserSeeder::class,                //  Usuarios con roles ya existentes
            RoomTypeSeeder::class,            //  Catálogos base
            RoomSeeder::class,                //  Habitaciones (dependen de RoomType)
            GuestSeeder::class,               //  Huéspedes de prueba
            ReservationSeeder::class,         //  Reservas (dependen de Guest + Room)
            InvoiceSeeder::class,             //  Facturas (dependen de Reservation)
            ShiftCloseSeeder::class,          //  Cierres de turno
            InventoryItemSeeder::class,       //  Inventario (V2, no bloquea)
        ]);

        $this->command->info('✅ Hostify — base de datos lista.');
    }
}