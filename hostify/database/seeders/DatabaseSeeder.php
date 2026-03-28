<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
        UserSeeder::class,
        RoomTypeSeeder::class,
        RoomSeeder::class,
        GuestSeeder::class,        
        ReservationSeeder::class,  
        InvoiceSeeder::class,      
        ShiftCloseSeeder::class,   
        InventoryItemSeeder::class,
        ]);

        $this->command->info('HotelX — base de datos lista con datos completos de prueba.');
    }
}