<?php

namespace Database\Seeders;

use App\Enums\InvoiceStatus;
use App\Enums\ReservationStatus;
use App\Models\Invoice;
use App\Models\Reservation;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        // Factura pagada para cada checked_out
        $checkedOut = Reservation::where('status', ReservationStatus::CheckedOut)
            ->doesntHave('invoice')
            ->with('charges')
            ->get();

        foreach ($checkedOut as $reservation) {
            $subtotal = $reservation->invoice_total;

            Invoice::factory()->create([
                'reservation_id' => $reservation->id,
                'subtotal'       => $subtotal,
                'taxes'          => 0,
                'total'          => $subtotal,
                'status'         => InvoiceStatus::Pagada->value,
            ]);
        }

        // Factura emitida (pendiente de pago) para activas
        $activas = Reservation::where('status', ReservationStatus::Activa)
            ->doesntHave('invoice')
            ->with('charges')
            ->get();

        foreach ($activas as $reservation) {
            $subtotal = $reservation->invoice_total;

            Invoice::factory()->create([
                'reservation_id' => $reservation->id,
                'subtotal'       => $subtotal,
                'taxes'          => 0,
                'total'          => $subtotal,
                'status'         => InvoiceStatus::Emitida->value,
            ]);
        }

        $total = Invoice::count();
        $this->command->info(" {$total} facturas generadas automáticamente.");
    }
}