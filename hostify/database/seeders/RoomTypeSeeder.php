<?php

namespace Database\Seeders;

use App\Models\RoomType;
use Illuminate\Database\Seeder;

class RoomTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name'        => 'Sencilla',
                'description' => 'Habitación para una persona con cama individual.',
                'base_price'  => 80000,
                'capacity'    => 1,
                'is_active'   => true,
            ],
            [
                'name'        => 'Doble',
                'description' => 'Habitación para dos personas con cama doble.',
                'base_price'  => 120000,
                'capacity'    => 2,
                'is_active'   => true,
            ],
            [
                'name'        => 'Triple',
                'description' => 'Habitación para tres personas.',
                'base_price'  => 160000,
                'capacity'    => 3,
                'is_active'   => true,
            ],
            [
                'name'        => 'Suite',
                'description' => 'Habitación premium con sala y jacuzzi.',
                'base_price'  => 280000,
                'capacity'    => 4,
                'is_active'   => true,
            ],
        ];

        foreach ($types as $type) {
            RoomType::firstOrCreate(['name' => $type['name']], $type);
        }
    }
}
