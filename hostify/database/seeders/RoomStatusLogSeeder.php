<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\RoomStatusLog;
use Illuminate\Database\Seeder;

class RoomStatusLogSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = Room::all();

        foreach ($rooms as $room) {
            // Solo crear log inicial si la habitación no tiene historial aún
            if (RoomStatusLog::where('room_id', $room->id)->exists()) {
                continue;
            }

            RoomStatusLog::create([
                'room_id'     => $room->id,
                'changed_by'  => null,         // Sistema — nadie específico
                'from_status' => 'libre',
                'to_status'   => $room->status, // Estado actual real de la habitación
                'source'      => 'system',
                'changed_at'  => $room->created_at,
            ]);
        }

        $this->command->info('✔ Log inicial de estados creado para ' . $rooms->count() . ' habitaciones.');
    }
}