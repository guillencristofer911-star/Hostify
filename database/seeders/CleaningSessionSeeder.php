<?php

namespace Database\Seeders;

use App\Enums\CleaningStatus;
use App\Models\CleaningSession;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;

class CleaningSessionSeeder extends Seeder
{
    public function run(): void
    {
        $admin    = User::where('email', 'admin@hostify.com')->first();
        $camarera = User::where('email', 'maria@hostify.com')->first();

        if (! $admin || ! $camarera) {
            $this->command->warn('CleaningSessionSeeder: usuarios no encontrados. Ejecuta UserSeeder primero.');
            return;
        }

        $rooms = Room::whereIn('number', ['101', '102', '103', '201', '202'])->get()->keyBy('number');

        if ($rooms->isEmpty()) {
            $this->command->warn('CleaningSessionSeeder: habitaciones no encontradas. Ejecuta RoomSeeder primero.');
            return;
        }

        $sessions = [
            // Pendientes — para hoy
            [
                'room_id'       => $rooms['101']->id,
                'assigned_to'   => $camarera->id,
                'assigned_by'   => $admin->id,
                'assigned_date' => today(),
                'status'        => CleaningStatus::Pendiente->value,
            ],
            [
                'room_id'       => $rooms['102']->id,
                'assigned_to'   => $camarera->id,
                'assigned_by'   => $admin->id,
                'assigned_date' => today(),
                'status'        => CleaningStatus::Pendiente->value,
            ],
            // En proceso — para hoy
            [
                'room_id'       => $rooms['103']->id,
                'assigned_to'   => $camarera->id,
                'assigned_by'   => $admin->id,
                'assigned_date' => today(),
                'status'        => CleaningStatus::EnProceso->value,
                'started_at'    => now()->subMinutes(20),
            ],
            // Terminadas — para hoy
            [
                'room_id'       => $rooms['201']->id,
                'assigned_to'   => $camarera->id,
                'assigned_by'   => $admin->id,
                'assigned_date' => today(),
                'status'        => CleaningStatus::Terminada->value,
                'started_at'    => now()->subMinutes(60),
                'finished_at'   => now()->subMinutes(30),
                'duration_minutes' => 30,
            ],
            // Historial — ayer
            [
                'room_id'       => $rooms['202']->id,
                'assigned_to'   => $camarera->id,
                'assigned_by'   => $admin->id,
                'assigned_date' => today()->subDay(),
                'status'        => CleaningStatus::Terminada->value,
                'started_at'    => now()->subDay()->setHour(9)->setMinute(0),
                'finished_at'   => now()->subDay()->setHour(9)->setMinute(25),
                'duration_minutes' => 25,
                'notes'         => 'Habitación dejada en buen estado.',
            ],
        ];

        foreach ($sessions as $session) {
            CleaningSession::create($session);
        }

        $this->command->info(' 5 sesiones de limpieza creadas (2 pendientes, 1 en proceso, 1 terminada hoy, 1 historial).');
    }
}