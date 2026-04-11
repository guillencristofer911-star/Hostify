<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $sencilla = RoomType::where('name', 'Sencilla')->first();
        $doble    = RoomType::where('name', 'Doble')->first();
        $triple   = RoomType::where('name', 'Triple')->first();
        $suite    = RoomType::where('name', 'Suite')->first();

        $rooms = [
            // Piso 1
            ['number' => '101', 'floor' => 1, 'room_type_id' => $doble->id],
            ['number' => '102', 'floor' => 1, 'room_type_id' => $doble->id],
            ['number' => '103', 'floor' => 1, 'room_type_id' => $sencilla->id],
            ['number' => '104', 'floor' => 1, 'room_type_id' => $sencilla->id],
            ['number' => '105', 'floor' => 1, 'room_type_id' => $triple->id],
            ['number' => '106', 'floor' => 1, 'room_type_id' => $triple->id],
            ['number' => '107', 'floor' => 1, 'room_type_id' => $doble->id],
            // Piso 2
            ['number' => '201', 'floor' => 2, 'room_type_id' => $doble->id],
            ['number' => '202', 'floor' => 2, 'room_type_id' => $doble->id],
            ['number' => '203', 'floor' => 2, 'room_type_id' => $sencilla->id],
            ['number' => '204', 'floor' => 2, 'room_type_id' => $sencilla->id],
            ['number' => '205', 'floor' => 2, 'room_type_id' => $triple->id],
            ['number' => '206', 'floor' => 2, 'room_type_id' => $triple->id],
            ['number' => '207', 'floor' => 2, 'room_type_id' => $doble->id],
            // Piso 3
            ['number' => '301', 'floor' => 3, 'room_type_id' => $doble->id],
            ['number' => '302', 'floor' => 3, 'room_type_id' => $doble->id],
            ['number' => '303', 'floor' => 3, 'room_type_id' => $sencilla->id],
            ['number' => '304', 'floor' => 3, 'room_type_id' => $sencilla->id],
            ['number' => '305', 'floor' => 3, 'room_type_id' => $triple->id],
            ['number' => '306', 'floor' => 3, 'room_type_id' => $triple->id],
            ['number' => '307', 'floor' => 3, 'room_type_id' => $doble->id],
            // Piso 4
            ['number' => '401', 'floor' => 4, 'room_type_id' => $doble->id],
            ['number' => '402', 'floor' => 4, 'room_type_id' => $doble->id],
            ['number' => '403', 'floor' => 4, 'room_type_id' => $sencilla->id],
            ['number' => '404', 'floor' => 4, 'room_type_id' => $triple->id],
            ['number' => '405', 'floor' => 4, 'room_type_id' => $triple->id],
            ['number' => '406', 'floor' => 4, 'room_type_id' => $suite->id],
            ['number' => '407', 'floor' => 4, 'room_type_id' => $suite->id],
        ];

        foreach ($rooms as $room) {
            Room::firstOrCreate(
                ['number' => $room['number']],
                array_merge($room, [
                    'status'    => 'libre',
                    'is_active' => true,
                ])
            );
        }

        $this->command->info(' 28 habitaciones creadas.');
    }
}
