<?php

namespace Database\Seeders;

use App\Enums\ReservationSource;
use App\Enums\ReservationStatus;
use App\Enums\RoomStatus;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        $createdBy = User::first()?->id;

        //  1. Historial pasado (últimos 90 días)
        Reservation::factory()
            ->count(120)
            ->checkedOut()
            ->create(['created_by' => $createdBy, 'source' => ReservationSource::ManualReception->value]);

        //  2. Activas ahora
        $roomsForActive = Room::where('is_active', true)
            ->inRandomOrder()
            ->take(15)
            ->pluck('id');

        foreach ($roomsForActive as $roomId) {
            Reservation::factory()
                ->activa()
                ->create([
                    'room_id'    => $roomId,
                    'created_by' => $createdBy,
                    'source'     => ReservationSource::ManualReception->value,
                ]);

            Room::find($roomId)?->updateStatus(RoomStatus::Ocupada);
        }

        //  3. Entrando HOY
        Reservation::factory()
            ->count(5)
            ->entrandoHoy()
            ->create(['created_by' => $createdBy, 'source' => ReservationSource::WebForm->value]);

        //  4. Saliendo HOY
        $roomsForCheckout = Room::where('is_active', true)
            ->whereNotIn('id', $roomsForActive)
            ->inRandomOrder()
            ->take(5)
            ->pluck('id');

        foreach ($roomsForCheckout as $roomId) {
            Reservation::factory()
                ->saliendoHoy()
                ->create([
                    'room_id'    => $roomId,
                    'created_by' => $createdBy,
                    'source'     => ReservationSource::ManualReception->value,
                ]);

            Room::find($roomId)?->updateStatus(RoomStatus::Ocupada);
        }

        //  5. Próximas aprobadas
        Reservation::factory()
            ->count(40)
            ->aprobada()
            ->create(['created_by' => $createdBy, 'source' => ReservationSource::WebForm->value]);

        //  6. Pendientes sin aprobar
        Reservation::factory()
            ->count(20)
            ->pendiente()
            ->create(['created_by' => $createdBy, 'source' => ReservationSource::WebForm->value]);

        //  7. Canceladas
        Reservation::factory()
            ->count(30)
            ->cancelada()
            ->create(['created_by' => $createdBy, 'source' => ReservationSource::ManualReception->value]);

        $total = Reservation::count();
        $this->command->info(" {$total} reservas creadas — siempre relativas a hoy.");
    }
}
