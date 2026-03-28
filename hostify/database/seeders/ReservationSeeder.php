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

        //  1. Historial pasado 
        Reservation::factory()
            ->count(15)
            ->checkedOut()
            ->create(['created_by' => $createdBy, 'source' => ReservationSource::ManualReception->value]);

        //  2. Activas ahora 
        // Usamos habitaciones específicas para que el panel las muestre ocupadas
        $roomsForActive = Room::where('is_active', true)
            ->inRandomOrder()
            ->take(4)
            ->pluck('id');

        foreach ($roomsForActive as $roomId) {
            Reservation::factory()
                ->activa()
                ->create([
                    'room_id'    => $roomId,
                    'created_by' => $createdBy,
                    'source'     => ReservationSource::ManualReception->value,
                ]);

            // Sincronizar estado físico de la habitación
            Room::find($roomId)?->updateStatus(RoomStatus::Ocupada);
        }

        //  3. Entrando HOY 
        Reservation::factory()
            ->count(2)
            ->entrandoHoy()
            ->create(['created_by' => $createdBy, 'source' => ReservationSource::WebForm->value]);

        //  4. Saliendo HOY 
        $roomsForCheckout = Room::where('is_active', true)
            ->whereNotIn('id', $roomsForActive)
            ->inRandomOrder()
            ->take(2)
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
            ->count(6)
            ->aprobada()
            ->create(['created_by' => $createdBy, 'source' => ReservationSource::WebForm->value]);

        //  6. Pendientes sin aprobar 
        Reservation::factory()
            ->count(3)
            ->pendiente()
            ->create(['created_by' => $createdBy, 'source' => ReservationSource::WebForm->value]);

        //  7. Canceladas 
        Reservation::factory()
            ->count(4)
            ->cancelada()
            ->create(['created_by' => $createdBy, 'source' => ReservationSource::ManualReception->value]);

        $total = Reservation::count();
        $this->command->info(" {$total} reservas creadas — siempre relativas a hoy.");
    }
}