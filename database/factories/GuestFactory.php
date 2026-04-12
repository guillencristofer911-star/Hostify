<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class GuestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'full_name'       => fake()->name(),
            'document_type'   => fake()->randomElement(['CC', 'CE', 'Pasaporte', 'NIT']),
            'document_number' => fake()->unique()->numerify('##########'),
            'phone'           => fake()->numerify('3#########'),
            'email'           => fake()->unique()->safeEmail(),
            'nationality'     => fake()->randomElement(['Colombiana', 'Venezolana', 'Ecuatoriana', 'Estadounidense', 'Española', 'Francesa', 'Mexicana']),
            'notes'           => fake()->optional()->sentence(),
            'is_active'       => true,
        ];
    }
}