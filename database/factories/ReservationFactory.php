<?php

namespace Database\Factories;

use App\Enums\ReservationSource;
use App\Enums\ReservationStatus;
use App\Models\Guest;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition(): array
    {
        $checkIn  = now()->subDays(rand(30, 60))->startOfDay();
        $checkOut = $checkIn->copy()->addDays(rand(1, 5));

        return [
            'guest_id'        => Guest::inRandomOrder()->value('id'),
            'room_id'         => Room::inRandomOrder()->value('id'),
            'created_by'      => User::inRandomOrder()->value('id'),
            'source'          => $this->faker->randomElement(ReservationSource::cases())->value,
            'status'          => ReservationStatus::CheckedOut->value,
            'check_in_date'   => $checkIn,
            'check_out_date'  => $checkOut,
            'actual_check_in' => $checkIn->copy()->setTime(rand(13, 18), rand(0, 59)),
            'actual_check_out'=> $checkOut->copy()->setTime(rand(9, 12), rand(0, 59)),
            'rate'            => $this->faker->randomElement([80000, 100000, 120000, 150000, 180000, 220000]),
        ];
    }

    //  States 

    /** Reserva con check-out completado — historial pasado */
    public function checkedOut(): static
    {
        return $this->state(function () {
            $checkIn  = now()->subDays(rand(8, 60))->startOfDay();
            $checkOut = $checkIn->copy()->addDays(rand(1, 5));

            return [
                'status'          => ReservationStatus::CheckedOut->value,
                'check_in_date'   => $checkIn,
                'check_out_date'  => $checkOut,
                'actual_check_in' => $checkIn->copy()->setTime(rand(13, 18), rand(0, 59)),
                'actual_check_out'=> $checkOut->copy()->setTime(rand(9, 12), rand(0, 59)),
            ];
        });
    }

    /** Reserva activa — huésped en casa ahora */
    public function activa(): static
    {
        return $this->state(function () {
            $checkIn  = now()->subDays(rand(1, 4))->startOfDay();
            $checkOut = now()->addDays(rand(1, 5))->startOfDay();

            return [
                'status'          => ReservationStatus::Activa->value,
                'check_in_date'   => $checkIn,
                'check_out_date'  => $checkOut,
                'actual_check_in' => $checkIn->copy()->setTime(rand(13, 18), rand(0, 59)),
                'actual_check_out'=> null,
            ];
        });
    }

    /** Reserva aprobada — próxima entrada */
    public function aprobada(): static
    {
        return $this->state(function () {
            $checkIn  = now()->addDays(rand(1, 15))->startOfDay();
            $checkOut = $checkIn->copy()->addDays(rand(1, 6));

            return [
                'status'          => ReservationStatus::Aprobada->value,
                'check_in_date'   => $checkIn,
                'check_out_date'  => $checkOut,
                'actual_check_in' => null,
                'actual_check_out'=> null,
            ];
        });
    }

    /** Reserva pendiente — sin aprobar todavía */
    public function pendiente(): static
    {
        return $this->state(function () {
            $checkIn  = now()->addDays(rand(3, 20))->startOfDay();
            $checkOut = $checkIn->copy()->addDays(rand(1, 6));

            return [
                'status'          => ReservationStatus::Pendiente->value,
                'check_in_date'   => $checkIn,
                'check_out_date'  => $checkOut,
                'actual_check_in' => null,
                'actual_check_out'=> null,
            ];
        });
    }

    /** Reserva cancelada */
    public function cancelada(): static
    {
        return $this->state(function () {
            $checkIn  = now()->subDays(rand(5, 40))->startOfDay();
            $checkOut = $checkIn->copy()->addDays(rand(1, 4));

            return [
                'status'          => ReservationStatus::Cancelada->value,
                'check_in_date'   => $checkIn,
                'check_out_date'  => $checkOut,
                'actual_check_in' => null,
                'actual_check_out'=> null,
            ];
        });
    }

    /** Reserva que entra HOY */
    public function entrandoHoy(): static
    {
        return $this->state(function () {
            $checkIn  = now()->startOfDay();
            $checkOut = $checkIn->copy()->addDays(rand(1, 4));

            return [
                'status'          => ReservationStatus::Aprobada->value,
                'check_in_date'   => $checkIn,
                'check_out_date'  => $checkOut,
                'actual_check_in' => null,
                'actual_check_out'=> null,
            ];
        });
    }

    /** Reserva que sale HOY */
    public function saliendoHoy(): static
    {
        return $this->state(function () {
            $checkIn  = now()->subDays(rand(1, 4))->startOfDay();
            $checkOut = now()->startOfDay();

            return [
                'status'          => ReservationStatus::Activa->value,
                'check_in_date'   => $checkIn,
                'check_out_date'  => $checkOut,
                'actual_check_in' => $checkIn->copy()->setTime(rand(13, 18), rand(0, 59)),
                'actual_check_out'=> null,
            ];
        });
    }
}