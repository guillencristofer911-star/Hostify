<?php

namespace Database\Factories;

use App\Enums\ShiftCloseStatus;
use App\Models\ShiftClose;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShiftCloseFactory extends Factory
{
    protected $model = ShiftClose::class;

    public function definition(): array
    {
        $cash       = $this->faker->randomFloat(2, 100000, 600000);
        $card       = $this->faker->randomFloat(2, 100000, 600000);
        $counted    = $cash + $this->faker->randomFloat(2, -8000, 8000);
        $difference = round($counted - $cash, 2);
        $margin     = 5000;

        $start = now()->subDays(rand(2, 14))->setTime(7, 0);
        $end   = $start->copy()->addHours(8);

        return [
            'opened_by'          => User::inRandomOrder()->value('id'),
            'closed_by'          => User::inRandomOrder()->value('id'),
            'validated_by'       => User::inRandomOrder()->value('id'),
            'shift_start'        => $start,
            'shift_end'          => $end,
            'total_cash_system'  => $cash,
            'total_card_system'  => $card,
            'total_cash_counted' => $counted,
            'difference'         => $difference,
            'within_margin'      => abs($difference) <= $margin,
            'margin_threshold'   => $margin,
            'observations'       => $this->faker->boolean(20)
                ? $this->faker->sentence()
                : null,
            'validated_at'       => $end->copy()->addMinutes(rand(10, 45)),
            'status'             => ShiftCloseStatus::Validado->value,
        ];
    }

    public function cerrado(): static
    {
        return $this->state(function () {
            $start = now()->subDay()->setTime(7, 0);
            $end   = $start->copy()->addHours(8);
            $cash  = $this->faker->randomFloat(2, 100000, 500000);
            $counted = $cash + $this->faker->randomFloat(2, -8000, 8000);

            return [
                'shift_start'        => $start,
                'shift_end'          => $end,
                'total_cash_system'  => $cash,
                'total_cash_counted' => $counted,
                'difference'         => round($counted - $cash, 2),
                'within_margin'      => abs($counted - $cash) <= 5000,
                'validated_by'       => null,
                'validated_at'       => null,
                'status'             => ShiftCloseStatus::Cerrado->value,
            ];
        });
    }

    public function abierto(): static
    {
        return $this->state(function () {
            return [
                'closed_by'          => null,
                'validated_by'       => null,
                'shift_start'        => now()->setTime(7, 0),
                'shift_end'          => null,
                'total_cash_system'  => 0,
                'total_card_system'  => 0,
                'total_cash_counted' => null,
                'difference'         => null,
                'within_margin'      => null,
                'validated_at'       => null,
                'status'             => ShiftCloseStatus::Abierto->value,
            ];
        });
    }
}