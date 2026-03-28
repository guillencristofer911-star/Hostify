<?php

namespace Database\Seeders;

use App\Models\Guest;
use Illuminate\Database\Seeder;

class GuestSeeder extends Seeder
{
    public function run(): void
    {
        // Huéspedes fijos para que ReservationSeeder tenga IDs estables
        $fixed = [
            ['full_name' => 'Carlos Ramírez',    'document_type' => 'cc',       'document_number' => '10234567', 'phone' => '3001112233', 'email' => 'carlos.ramirez@hotelx.test',    'nationality' => 'Colombiana'],
            ['full_name' => 'Valentina Torres',  'document_type' => 'cc',       'document_number' => '20345678', 'phone' => '3012223344', 'email' => 'valentina.torres@hotelx.test',  'nationality' => 'Colombiana'],
            ['full_name' => 'Andrés Morales',    'document_type' => 'cc',       'document_number' => '30456789', 'phone' => '3023334455', 'email' => 'andres.morales@hotelx.test',    'nationality' => 'Colombiana'],
            ['full_name' => 'James Wilson',      'document_type' => 'passport', 'document_number' => 'US123456', 'phone' => '3123334455', 'email' => 'james.wilson@hotelx.test',      'nationality' => 'Estadounidense'],
            ['full_name' => 'Sophie Dupont',     'document_type' => 'passport', 'document_number' => 'FR789012', 'phone' => '3134445566', 'email' => 'sophie.dupont@hotelx.test',     'nationality' => 'Francesa'],
        ];

        foreach ($fixed as $data) {
            Guest::firstOrCreate(
                ['document_number' => $data['document_number']],
                array_merge($data, ['is_active' => true])
            );
        }

        // 95 huéspedes aleatorios adicionales con Faker (total ~100)
        Guest::factory()->count(95)->create();

        $total = Guest::count();
        $this->command->info(" {$total} huéspedes creados: 5 fijos + 95 aleatorios.");
    }
}
