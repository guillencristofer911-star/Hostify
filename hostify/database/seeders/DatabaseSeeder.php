<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoomTypeSeeder::class,       // 1. Tipos de habitación
            RoomSeeder::class,           // 2. Habitaciones (depende de tipos)
            UserSeeder::class,           // 3. Usuarios y roles
            InventoryItemSeeder::class,  // 4. Items de inventario
            GuestSeeder::class,          // 5. Huéspedes
            ReservationSeeder::class,    // 6. Reservas (depende de huéspedes y habitaciones)
            InvoiceSeeder::class,        // 7. Facturas (depende de reservas)
            ShiftCloseSeeder::class,     // 8. Cierres de turno (depende de usuarios)
        ]);

        $this->command->info('HotelX — base de datos lista con datos completos de prueba.');
    }
}