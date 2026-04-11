<?php

namespace Database\Factories;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    private static int $invoiceCounter = 1;

    public function definition(): array
    {
        $subtotal = round(rand(80000, 900000) / 100) * 100;

        return [
            'invoice_number' => 'FAC-' . str_pad(self::$invoiceCounter++, 6, '0', STR_PAD_LEFT),
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