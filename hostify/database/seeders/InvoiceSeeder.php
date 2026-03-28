<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Invoice;
use App\Models\Reservation;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {

        $reservations = Reservation::where('status', 'checked_out')
            ->whereDoesntHave('invoice') 
            ->get();

        $counter = 1;

        foreach ($reservations as $reservation) {
            // Calcular noches
            $checkIn  = \Carbon\Carbon::parse($reservation->check_in);
            $checkOut = \Carbon\Carbon::parse($reservation->check_out);
            $nights   = max(1, $checkIn->diffInDays($checkOut));

            // Precio por noche desde la habitación
            $pricePerNight = $reservation->room->price_per_night ?? 80000;
            $subtotal      = $nights * $pricePerNight;
            $taxes         = 0;
            $total         = $subtotal + $taxes;

            Invoice::create([
                'invoice_number' => 'FAC-' . str_pad($counter++, 6, '0', STR_PAD_LEFT),
                'reservation_id' => $reservation->id,
                'subtotal'       => $subtotal,
                'taxes'          => $taxes,
                'total'          => $total,
                'status'         => 'pagada',
            ]);
        }

        $count = $counter - 1;
        $this->command->info(" {$count} facturas creadas — solo para reservas checked_out.");
    }
}