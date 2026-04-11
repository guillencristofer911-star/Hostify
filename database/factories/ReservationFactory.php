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
        $sources = array_column(ReservationSource::cases(), 'value');
        $rates   = [80000, 100000, 120000, 150000, 180000, 220000];

        $checkIn  = now()->subDays(rand(30, 60))->startOfDay();
        $checkOut = $checkIn->copy()->addDays(rand(1, 5));

        return [
            'guest_id'        => Guest::inRandomOrder()->value('id'),
            'room_id'         => Room::inRandomOrder()->value('id'),
            'created_by'      => User::inRandomOrder()->value('id'),
            'source'          => $sources[array_rand($sources)],
            'status'          => ReservationStatus::CheckedOut->value,
            'check_in_date'   => $checkIn,
            'check_out_date'  => $checkOut,
            'actual_check_in' => $checkIn->copy()->setTime(rand(13, 18), rand(0, 59)),
            'actual_check_out'=> $checkOut->copy()->setTime(rand(9, 12), rand(0, 59)),
            'rate'            => $rates[array_rand($rates)],
        ];
    }

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