<?php

namespace Database\Seeders;

use App\Models\ShiftClose;
use App\Models\User;
use Illuminate\Database\Seeder;

class ShiftCloseSeeder extends Seeder
{
    public function run(): void
    {
        $admin      = User::where('email', 'admin@hotelx.com')->first();
        $ana        = User::where('email', 'ana@hotelx.com')->first();
        $supervisor = User::where('email', 'carlos@hotelx.com')->first();

        $shifts = [
            // Turnos validados — semana pasada
            [
                'opened_by'          => $ana->id,
                'closed_by'          => $ana->id,
                'validated_by'       => $supervisor->id,
                'shift_start'        => '2026-03-21 07:00:00',
                'shift_end'          => '2026-03-21 15:00:00',
                'total_cash_system'  => 320000,
                'total_card_system'  => 480000,
                'total_cash_counted' => 318000,
                'difference'         => -2000,
                'within_margin'      => true,
                'margin_threshold'   => 5000,
                'observations'       => 'Turno sin novedades.',
                'validated_at'       => '2026-03-21 15:30:00',
                'status'             => 'validado',
            ],
            [
                'opened_by'          => $admin->id,
                'closed_by'          => $admin->id,
                'validated_by'       => $supervisor->id,
                'shift_start'        => '2026-03-21 15:00:00',
                'shift_end'          => '2026-03-21 23:00:00',
                'total_cash_system'  => 560000,
                'total_card_system'  => 340000,
                'total_cash_counted' => 560000,
                'difference'         => 0,
                'within_margin'      => true,
                'margin_threshold'   => 5000,
                'observations'       => null,
                'validated_at'       => '2026-03-21 23:30:00',
                'status'             => 'validado',
            ],
            [
                'opened_by'          => $ana->id,
                'closed_by'          => $ana->id,
                'validated_by'       => $supervisor->id,
                'shift_start'        => '2026-03-22 07:00:00',
                'shift_end'          => '2026-03-22 15:00:00',
                'total_cash_system'  => 190000,
                'total_card_system'  => 620000,
                'total_cash_counted' => 204000,
                'difference'         => 14000,
                'within_margin'      => false,
                'margin_threshold'   => 5000,
                'observations'       => 'Diferencia detectada. Se revisó y corrigió en siguiente turno.',
                'validated_at'       => '2026-03-22 15:45:00',
                'status'             => 'validado',
            ],
            [
                'opened_by'          => $admin->id,
                'closed_by'          => $admin->id,
                'validated_by'       => $supervisor->id,
                'shift_start'        => '2026-03-23 07:00:00',
                'shift_end'          => '2026-03-23 15:00:00',
                'total_cash_system'  => 410000,
                'total_card_system'  => 395000,
                'total_cash_counted' => 409000,
                'difference'         => -1000,
                'within_margin'      => true,
                'margin_threshold'   => 5000,
                'observations'       => null,
                'validated_at'       => '2026-03-23 15:20:00',
                'status'             => 'validado',
            ],
            [
                'opened_by'          => $ana->id,
                'closed_by'          => $ana->id,
                'validated_by'       => $supervisor->id,
                'shift_start'        => '2026-03-25 07:00:00',
                'shift_end'          => '2026-03-25 15:00:00',
                'total_cash_system'  => 280000,
                'total_card_system'  => 510000,
                'total_cash_counted' => 280000,
                'difference'         => 0,
                'within_margin'      => true,
                'margin_threshold'   => 5000,
                'observations'       => null,
                'validated_at'       => '2026-03-25 15:10:00',
                'status'             => 'validado',
            ],
            // Turno cerrado — pendiente de validar
            [
                'opened_by'          => $ana->id,
                'closed_by'          => $ana->id,
                'validated_by'       => null,
                'shift_start'        => '2026-03-27 07:00:00',
                'shift_end'          => '2026-03-27 15:00:00',
                'total_cash_system'  => 350000,
                'total_card_system'  => 275000,
                'total_cash_counted' => 347000,
                'difference'         => -3000,
                'within_margin'      => true,
                'margin_threshold'   => 5000,
                'observations'       => 'Check-in tardío hab. 102.',
                'validated_at'       => null,
                'status'             => 'cerrado',
            ],
            // Turno abierto — turno actual
            [
                'opened_by'          => $admin->id,
                'closed_by'          => null,
                'validated_by'       => null,
                'shift_start'        => '2026-03-28 07:00:00',
                'shift_end'          => null,
                'total_cash_system'  => 0,
                'total_card_system'  => 0,
                'total_cash_counted' => null,
                'difference'         => null,
                'within_margin'      => null,
                'margin_threshold'   => 5000,
                'observations'       => null,
                'validated_at'       => null,
                'status'             => 'abierto',
            ],
        ];

        foreach ($shifts as $shift) {
            ShiftClose::create($shift);
        }

        $this->command->info('7 cierres de turno creados (5 validados, 1 cerrado, 1 abierto).');
    }
}