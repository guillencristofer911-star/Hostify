<?php

namespace Database\Factories;

use App\Models\Guest;
use Illuminate\Database\Eloquent\Factories\Factory;

class GuestFactory extends Factory
{
    protected $model = Guest::class;

    // Tipos de documento con peso realista para Colombia
    private array $documentTypes = [
        'cc', 'cc', 'cc', 'cc', 'cc', 'cc',   // 60% cédula colombiana
        'passport', 'passport',                  // 20% pasaporte
        'cedula_extranjeria',                    // 10% cédula extranjería
        'ti',                                    // 10% tarjeta identidad
    ];

    private array $nationalities = [
        'Colombiana', 'Colombiana', 'Colombiana', 'Colombiana', 'Colombiana',
        'Venezolana', 'Estadounidense', 'Francesa', 'Española', 'Mexicana',
        'Argentina', 'Peruana', 'Ecuatoriana', 'Brasileña', 'Alemana',
    ];

    public function definition(): array
    {
        $docType = $this->faker->randomElement($this->documentTypes);

        $docNumber = match ($docType) {
            'cc'                 => (string) $this->faker->unique()->numberBetween(10000000, 99999999),
            'ti'                 => (string) $this->faker->unique()->numberBetween(100000000, 999999999),
            'passport'           => strtoupper($this->faker->bothify('??######')),
            'cedula_extranjeria' => strtoupper($this->faker->bothify('?E######')),
            default              => (string) $this->faker->unique()->numberBetween(10000000, 99999999),
        };

        return [
            'full_name'       => $this->faker->name(),
            'document_type'   => $docType,
            'document_number' => $docNumber,
            'phone'           => '3' . $this->faker->numerify('#########'),
            'email'           => $this->faker->unique()->safeEmail(),
            'nationality'     => $this->faker->randomElement($this->nationalities),
            'is_active'       => true,
        ];
    }
}