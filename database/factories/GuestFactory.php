<?php

namespace Database\Factories;

use App\Models\Guest;
use Illuminate\Database\Eloquent\Factories\Factory;

class GuestFactory extends Factory
{
    protected $model = Guest::class;

    public function definition(): array
    {
        $documentTypes = [
            'cc', 'cc', 'cc', 'cc', 'cc', 'cc',
            'passport', 'passport',
            'cedula_extranjeria',
            'ti',
        ];

        $nationalities = [
            'Colombiana', 'Colombiana', 'Colombiana', 'Colombiana', 'Colombiana',
            'Venezolana', 'Estadounidense', 'Francesa', 'Española', 'Mexicana',
            'Argentina', 'Peruana', 'Ecuatoriana', 'Brasileña', 'Alemana',
        ];

        $names = [
            'Juan García', 'María López', 'Carlos Martínez', 'Ana Rodríguez',
            'Luis Pérez', 'Laura Sánchez', 'José González', 'Isabel Díaz',
            'Pedro Jiménez', 'Carmen Ruiz', 'Miguel Hernández', 'Elena Moreno',
            'Antonio Muñoz', 'Sofía Álvarez', 'Francisco Romero', 'Lucía Torres',
            'Manuel Flores', 'Victoria Ramírez', 'David Ortega', 'Marta Vargas',
            'Andrés Castro', 'Valentina Ramos', 'Felipe Mendoza', 'Daniela Cruz',
            'Sebastián Reyes', 'Camila Morales', 'Alejandro Jiménez', 'Gabriela Herrera',
            'Ricardo Medina', 'Patricia Suárez', 'Fernando Aguilar', 'Natalia Vega',
            'Eduardo Guzmán', 'Adriana Ríos', 'Sergio Varela', 'Mónica Castillo',
            'Roberto Peña', 'Claudia Mendez', 'Javier Ibáñez', 'Sandra Delgado',
        ];

        $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $docType  = $documentTypes[array_rand($documentTypes)];

        $docNumber = match ($docType) {
            'cc'                 => (string) random_int(10_000_000, 1_999_999_999),
            'ti'                 => (string) random_int(100_000_000, 999_999_999),
            'passport'           => $letters[random_int(0, 25)] . $letters[random_int(0, 25)] . random_int(100000, 999999),
            'cedula_extranjeria' => $letters[random_int(0, 25)] . 'E' . random_int(100000, 999999),
            default              => (string) random_int(10_000_000, 1_999_999_999),
        };

        $name  = $names[array_rand($names)];
        $rand  = random_int(10000, 99999);
        $email = strtolower(
            str_replace([' ', 'á','é','í','ó','ú','ñ'], ['.','a','e','i','o','u','n'], $name)
        ) . $rand . '@hotelx.test';

        return [
            'full_name'       => $name,
            'document_type'   => $docType,
            'document_number' => $docNumber,
            'phone'           => '3' . random_int(100_000_000, 999_999_999),
            'email'           => $email,
            'nationality'     => $nationalities[array_rand($nationalities)],
            'is_active'       => true,
        ];
    }
}