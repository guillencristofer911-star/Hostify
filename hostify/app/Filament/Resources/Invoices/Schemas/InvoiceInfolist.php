<?php

namespace App\Filament\Resources\Invoices\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class InvoiceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('reservation.id')
                    ->label('Reservation'),
                TextEntry::make('invoice_number'),
                TextEntry::make('subtotal')
                    ->numeric(),
                TextEntry::make('taxes')
                    ->numeric(),
                TextEntry::make('total')
                    ->numeric(),
                TextEntry::make('status'),
                TextEntry::make('sent_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('sent_to_email')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
