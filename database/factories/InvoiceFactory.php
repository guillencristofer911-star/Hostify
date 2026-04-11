<?php

namespace Database\Factories;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 80000, 900000);

        return [
            'invoice_number' => 'FAC-' . $this->faker->unique()->numerify('######'),
            'subtotal'       => $subtotal,
            'taxes'          => 0,
            'total'          => $subtotal,
            'status'         => 'pagada',
        ];
    }

    public function pendiente(): static
    {
        return $this->state(['status' => 'pendiente']);
    }

    public function anulada(): static
    {
        return $this->state(['status' => 'anulada']);
    }
}