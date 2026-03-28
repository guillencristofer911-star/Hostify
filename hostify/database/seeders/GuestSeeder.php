<?php

namespace Database\Seeders;

use App\Models\Guest;
use Illuminate\Database\Seeder;

class GuestSeeder extends Seeder
{
    public function run(): void
    {
        $guests = [
            ['full_name' => 'Carlos Ramírez',       'document_type' => 'cc',                  'document_number' => '10234567',  'phone' => '3001112233', 'email' => 'carlos.ramirez@gmail.com',    'nationality' => 'Colombiana'],
            ['full_name' => 'Valentina Torres',      'document_type' => 'cc',                  'document_number' => '20345678',  'phone' => '3012223344', 'email' => 'valentina.torres@gmail.com',  'nationality' => 'Colombiana'],
            ['full_name' => 'Andrés Morales',        'document_type' => 'cc',                  'document_number' => '30456789',  'phone' => '3023334455', 'email' => 'andres.morales@yahoo.com',    'nationality' => 'Colombiana'],
            ['full_name' => 'Luisa Fernández',       'document_type' => 'cc',                  'document_number' => '40567890',  'phone' => '3034445566', 'email' => 'luisa.fernandez@hotmail.com', 'nationality' => 'Colombiana'],
            ['full_name' => 'Miguel Ospina',         'document_type' => 'cc',                  'document_number' => '50678901',  'phone' => '3045556677', 'email' => 'miguel.ospina@gmail.com',     'nationality' => 'Colombiana'],
            ['full_name' => 'Daniela Herrera',       'document_type' => 'cc',                  'document_number' => '60789012',  'phone' => '3056667788', 'email' => 'daniela.herrera@gmail.com',   'nationality' => 'Colombiana'],
            ['full_name' => 'Jorge Castro',          'document_type' => 'cc',                  'document_number' => '70890123',  'phone' => '3067778899', 'email' => 'jorge.castro@outlook.com',    'nationality' => 'Colombiana'],
            ['full_name' => 'Paola Ríos',            'document_type' => 'cc',                  'document_number' => '80901234',  'phone' => '3078889900', 'email' => 'paola.rios@gmail.com',        'nationality' => 'Colombiana'],
            ['full_name' => 'Ricardo Vargas',        'document_type' => 'cc',                  'document_number' => '91012345',  'phone' => '3089990011', 'email' => 'ricardo.vargas@gmail.com',    'nationality' => 'Colombiana'],
            ['full_name' => 'Natalia Gómez',         'document_type' => 'cc',                  'document_number' => '11123456',  'phone' => '3090001122', 'email' => 'natalia.gomez@gmail.com',     'nationality' => 'Colombiana'],
            ['full_name' => 'Felipe Salazar',        'document_type' => 'cc',                  'document_number' => '12234567',  'phone' => '3101112233', 'email' => 'felipe.salazar@yahoo.com',    'nationality' => 'Colombiana'],
            ['full_name' => 'Sara Peña',             'document_type' => 'cc',                  'document_number' => '13345678',  'phone' => '3112223344', 'email' => 'sara.pena@gmail.com',         'nationality' => 'Colombiana'],
            ['full_name' => 'James Wilson',          'document_type' => 'passport',             'document_number' => 'US123456',  'phone' => '3123334455', 'email' => 'james.wilson@gmail.com',      'nationality' => 'Estadounidense'],
            ['full_name' => 'Sophie Dupont',         'document_type' => 'passport',             'document_number' => 'FR789012',  'phone' => '3134445566', 'email' => 'sophie.dupont@gmail.com',     'nationality' => 'Francesa'],
            ['full_name' => 'Carlos Mendez',         'document_type' => 'cedula_extranjeria',   'document_number' => 'VE456789',  'phone' => '3145556677', 'email' => 'carlos.mendez@gmail.com',     'nationality' => 'Venezolana'],
            ['full_name' => 'Isabella Martínez',     'document_type' => 'cc',                  'document_number' => '14456789',  'phone' => '3156667788', 'email' => 'isabella.martinez@gmail.com', 'nationality' => 'Colombiana'],
            ['full_name' => 'Sebastián Cárdenas',    'document_type' => 'cc',                  'document_number' => '15567890',  'phone' => '3167778899', 'email' => 'sebastian.cardenas@gmail.com','nationality' => 'Colombiana'],
            ['full_name' => 'Camila Jiménez',        'document_type' => 'cc',                  'document_number' => '16678901',  'phone' => '3178889900', 'email' => 'camila.jimenez@hotmail.com',  'nationality' => 'Colombiana'],
            ['full_name' => 'Tomás Aguilar',         'document_type' => 'cc',                  'document_number' => '17789012',  'phone' => '3189990011', 'email' => 'tomas.aguilar@gmail.com',     'nationality' => 'Colombiana'],
            ['full_name' => 'Mariana Reyes',         'document_type' => 'cc',                  'document_number' => '18890123',  'phone' => '3190001122', 'email' => 'mariana.reyes@gmail.com',     'nationality' => 'Colombiana'],
        ];

        foreach ($guests as $guest) {
            Guest::firstOrCreate(
                ['document_number' => $guest['document_number']],
                array_merge($guest, ['is_active' => true])
            );
        }

        $this->command->info('20 huéspedes creados.');
    }
}