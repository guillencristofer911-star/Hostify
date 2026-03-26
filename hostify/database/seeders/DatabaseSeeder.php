<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoomTypeSeeder::class,       // 1. Tipos primero
            RoomSeeder::class,           // 2. Habitaciones (necesita tipos)
            UserSeeder::class,           // 3. Usuarios + roles
            InventoryItemSeeder::class,  // 4. Artículos inventario
        ]);

        $this->command->info(' HotelX listo con datos iniciales!');
    }
}
