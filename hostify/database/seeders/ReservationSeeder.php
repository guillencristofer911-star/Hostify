<?php

namespace Database\Seeders;

use App\Models\Guest;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        $recepcionista = User::where('email', 'ana@hotelx.com')->first();
        $guests        = Guest::all()->keyBy('document_number');
        $rooms         = Room::with('roomType')->get()->keyBy('number');

        $reservations = [
            // CHECKED_OUT — historial pasado
            ['guest' => '10234567', 'room' => '101', 'check_in' => '2026-03-01', 'check_out' => '2026-03-03', 'status' => 'checked_out', 'actual_check_in' => '2026-03-01 14:10:00', 'actual_check_out' => '2026-03-03 11:00:00'],
            ['guest' => '20345678', 'room' => '103', 'check_in' => '2026-03-02', 'check_out' => '2026-03-05', 'status' => 'checked_out', 'actual_check_in' => '2026-03-02 15:30:00', 'actual_check_out' => '2026-03-05 10:45:00'],
            ['guest' => '30456789', 'room' => '105', 'check_in' => '2026-03-05', 'check_out' => '2026-03-08', 'status' => 'checked_out', 'actual_check_in' => '2026-03-05 16:00:00', 'actual_check_out' => '2026-03-08 11:30:00'],
            ['guest' => '40567890', 'room' => '201', 'check_in' => '2026-03-10', 'check_out' => '2026-03-12', 'status' => 'checked_out', 'actual_check_in' => '2026-03-10 13:00:00', 'actual_check_out' => '2026-03-12 10:00:00'],
            ['guest' => '50678901', 'room' => '406', 'check_in' => '2026-03-12', 'check_out' => '2026-03-15', 'status' => 'checked_out', 'actual_check_in' => '2026-03-12 14:00:00', 'actual_check_out' => '2026-03-15 11:00:00'],
            ['guest' => '60789012', 'room' => '203', 'check_in' => '2026-03-15', 'check_out' => '2026-03-17', 'status' => 'checked_out', 'actual_check_in' => '2026-03-15 15:00:00', 'actual_check_out' => '2026-03-17 10:30:00'],
            ['guest' => 'US123456', 'room' => '407', 'check_in' => '2026-03-18', 'check_out' => '2026-03-22', 'status' => 'checked_out', 'actual_check_in' => '2026-03-18 17:00:00', 'actual_check_out' => '2026-03-22 12:00:00'],
            ['guest' => 'FR789012', 'room' => '302', 'check_in' => '2026-03-20', 'check_out' => '2026-03-24', 'status' => 'checked_out', 'actual_check_in' => '2026-03-20 14:30:00', 'actual_check_out' => '2026-03-24 11:00:00'],
            // ACTIVA — actualmente hospedados
            ['guest' => '70890123', 'room' => '102', 'check_in' => '2026-03-26', 'check_out' => '2026-03-29', 'status' => 'activa', 'actual_check_in' => '2026-03-26 15:00:00', 'actual_check_out' => null],
            ['guest' => '80901234', 'room' => '204', 'check_in' => '2026-03-27', 'check_out' => '2026-03-30', 'status' => 'activa', 'actual_check_in' => '2026-03-27 14:00:00', 'actual_check_out' => null],
            ['guest' => '91012345', 'room' => '305', 'check_in' => '2026-03-27', 'check_out' => '2026-03-31', 'status' => 'activa', 'actual_check_in' => '2026-03-27 16:30:00', 'actual_check_out' => null],
            ['guest' => 'VE456789', 'room' => '401', 'check_in' => '2026-03-28', 'check_out' => '2026-04-02', 'status' => 'activa', 'actual_check_in' => '2026-03-28 13:00:00', 'actual_check_out' => null],
            // APROBADAS — próximas entradas
            ['guest' => '11123456', 'room' => '104', 'check_in' => '2026-03-29', 'check_out' => '2026-04-01', 'status' => 'aprobada', 'actual_check_in' => null, 'actual_check_out' => null],
            ['guest' => '12234567', 'room' => '306', 'check_in' => '2026-03-30', 'check_out' => '2026-04-03', 'status' => 'aprobada', 'actual_check_in' => null, 'actual_check_out' => null],
            ['guest' => '13345678', 'room' => '202', 'check_in' => '2026-04-01', 'check_out' => '2026-04-04', 'status' => 'aprobada', 'actual_check_in' => null, 'actual_check_out' => null],
            ['guest' => '14456789', 'room' => '403', 'check_in' => '2026-04-02', 'check_out' => '2026-04-05', 'status' => 'aprobada', 'actual_check_in' => null, 'actual_check_out' => null],
            ['guest' => '15567890', 'room' => '107', 'check_in' => '2026-04-05', 'check_out' => '2026-04-08', 'status' => 'aprobada', 'actual_check_in' => null, 'actual_check_out' => null],
            // CANCELADAS
            ['guest' => '16678901', 'room' => '303', 'check_in' => '2026-03-20', 'check_out' => '2026-03-22', 'status' => 'cancelada', 'actual_check_in' => null, 'actual_check_out' => null],
            ['guest' => '17789012', 'room' => '205', 'check_in' => '2026-03-25', 'check_out' => '2026-03-27', 'status' => 'cancelada', 'actual_check_in' => null, 'actual_check_out' => null],
        ];

        foreach ($reservations as $data) {
            $guest = $guests[$data['guest']] ?? null;
            $room  = $rooms[$data['room']] ?? null;

            if (!$guest || !$room) continue;

            $rate = $room->roomType->base_price ?? 80000;

            Reservation::firstOrCreate(
                [
                    'guest_id'      => $guest->id,
                    'room_id'       => $room->id,
                    'check_in_date' => $data['check_in'],
                ],
                [
                    'check_out_date'  => $data['check_out'],
                    'status'          => $data['status'],
                    'source'          => 'manual_reception',
                    'created_by'      => $recepcionista?->id,
                    'rate'            => $rate,
                    'actual_check_in' => $data['actual_check_in'],
                    'actual_check_out'=> $data['actual_check_out'],
                ]
            );
        }

        $this->command->info('19 reservas creadas (checked_out / activa / aprobada / cancelada).');
    }
}