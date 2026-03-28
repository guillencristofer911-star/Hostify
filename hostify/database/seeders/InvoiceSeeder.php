<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Reservation;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $reservations = Reservation::whereIn('status', ['checked_out', 'activa'])
            ->with('guest')
            ->get();

        $counter = 1;

        foreach ($reservations as $reservation) {
            $subtotal = $reservation->room_total ?? 0;
            $taxes    = round($subtotal * 0.19, 2);
            $total    = $subtotal + $taxes;
            $status   = $reservation->status === 'checked_out' ? 'pagada' : 'emitida';

            Invoice::firstOrCreate(
                ['reservation_id' => $reservation->id],
                [
                    'invoice_number' => 'FAC-' . str_pad($counter, 4, '0', STR_PAD_LEFT),
                    'subtotal'       => $subtotal,
                    'taxes'          => $taxes,
                    'total'          => $total,
                    'status'         => $status,
                ]
            );

            $counter++;
        }

        $this->command->info('Facturas creadas para reservas checked_out y activas.');
    }
}