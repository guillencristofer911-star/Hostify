<?php

namespace Database\Factories;

use App\Models\Guest;
use Illuminate\Database\Eloquent\Factories\Factory;

class GuestFactory extends Factory
{
    protected $model = Guest::class;

    private array $documentTypes = [
        'cc', 'cc', 'cc', 'cc', 'cc', 'cc',
        'passport', 'passport',
        'cedula_extranjeria',
        'ti',
    ];

    private array $nationalities = [
        'Colombiana', 'Colombiana', 'Colombiana', 'Colombiana', 'Colombiana',
        'Venezolana', 'Estadounidense', 'Francesa', 'Española', 'Mexicana',
        'Argentina', 'Peruana', 'Ecuatoriana', 'Brasileña', 'Alemana',
    ];

    public function definition(): array
    {
        $documentTypes = $this->documentTypes;
        $nationalities  = $this->nationalities;

        $docType = $this->faker->randomElement($documentTypes);

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
            'nationality'     => $this->faker->randomElement($nationalities),
            'is_active'       => true,
        ];
    }
}