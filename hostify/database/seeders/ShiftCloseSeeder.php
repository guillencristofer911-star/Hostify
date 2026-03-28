<?php

namespace Database\Seeders;

use App\Models\ShiftClose;
use App\Models\User;
use Illuminate\Database\Seeder;

class ShiftCloseSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('⚠️  No hay usuarios. Ejecuta UserSeeder primero.');
            return;
        }

        //  1. Turnos validados — últimos 14 días (mañana y tarde) 
        ShiftClose::factory()
            ->count(14)
            ->create([
                'opened_by'    => $users->random()->id,
                'closed_by'    => $users->random()->id,
                'validated_by' => $users->random()->id,
            ]);

        //  2. Turno cerrado de ayer — pendiente de validar 
        ShiftClose::factory()
            ->cerrado()
            ->create([
                'opened_by' => $users->random()->id,
                'closed_by' => $users->random()->id,
            ]);

        //  3. Turno abierto HOY — el turno actual 
        // Solo crear si no existe ya uno abierto
        $hayAbierto = ShiftClose::where('status', 'abierto')->exists();

        if (! $hayAbierto) {
            ShiftClose::factory()
                ->abierto()
                ->create(['opened_by' => $users->first()->id]);
        }

        $total = ShiftClose::count();
        $this->command->info(" {$total} turnos creados — 14 validados, 1 cerrado, 1 abierto.");
    }
}