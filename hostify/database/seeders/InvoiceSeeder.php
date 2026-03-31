<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Invoice;
use App\Models\Reservation;
use Carbon\Carbon;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $reservations = Reservation::with(['room.roomType', 'guest'])
            ->where('status', 'checked_out')
            ->whereDoesntHave('invoice')
            ->get();

        $counter = Invoice::max('invoice_number')
            ? (int) filter_var(Invoice::max('invoice_number'), FILTER_SANITIZE_NUMBER_INT) + 1
            : 1;

        foreach ($reservations as $reservation) {
            // Guardia: si no tiene habitación o huésped, saltar
            if (! $reservation->room || ! $reservation->guest) {
                $this->command->warn("Reserva {$reservation->id} sin habitación o huésped — omitida.");
                continue;
            }

            $checkIn  = Carbon::parse($reservation->check_in_date);
            $checkOut = Carbon::parse($reservation->check_out_date);
            $nights   = max(1, $checkIn->diffInDays($checkOut));

            $pricePerNight = $reservation->rate
                ?? $reservation->room->roomType->base_price
                ?? 80000;

            $subtotal    = $nights * $pricePerNight;
            $extrasTotal = 0; // Se sumará cuando existan charges reales
            $taxes       = 0;
            $total       = $subtotal + $extrasTotal + $taxes;

            Invoice::create([
                'invoice_number' => 'FAC-' . str_pad($counter++, 6, '0', STR_PAD_LEFT),
                'reservation_id' => $reservation->id,
                'guest_id'       => $reservation->guest_id,
                'subtotal'       => $subtotal,
                'extras_total'   => $extrasTotal,
                'taxes'          => $taxes,
                'total'          => $total,
                'status'         => 'pagada',
                'issued_at'      => $reservation->actual_check_out ?? now(),
            ]);
        }

        $count = $counter - 1;
        $this->command->info("✔ {$count} facturas creadas — solo reservas checked_out.");
    }
}