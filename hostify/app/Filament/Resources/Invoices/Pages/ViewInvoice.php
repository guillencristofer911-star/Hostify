<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Resources\Invoices\InvoiceResource;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('imprimir')
                ->label('Imprimir factura')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->action(fn () => $this->js('window.print()')),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Datos de la factura')
                ->icon('heroicon-o-document-text')
                ->columns(3)
                ->schema([
                    TextEntry::make('invoice_number')
                        ->label('# Factura')
                        ->weight('bold')
                        ->icon('heroicon-o-document-text'),

                    TextEntry::make('status')
                        ->label('Estado')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'pagada'   => 'success',
                            'emitida'  => 'info',
                            'borrador' => 'warning',
                            'anulada'  => 'danger',
                            default    => 'gray',
                        })
                        ->formatStateUsing(fn (string $state): string => match ($state) {
                            'pagada'   => 'Pagada',
                            'emitida'  => 'Emitida',
                            'borrador' => 'Borrador',
                            'anulada'  => 'Anulada',
                            default    => $state,
                        }),

                    TextEntry::make('created_at')
                        ->label('Fecha de emisión')
                        ->dateTime('d/m/Y H:i'),
                ]),

            Section::make('Huésped')
                ->icon('heroicon-o-user')
                ->columns(2)
                ->schema([
                    TextEntry::make('reservation.guest.full_name')
                        ->label('Nombre completo')
                        ->weight('bold'),

                    TextEntry::make('reservation.guest.document_number')
                        ->label('Documento'),

                    TextEntry::make('reservation.guest.phone')
                        ->label('Teléfono'),

                    TextEntry::make('reservation.guest.email')
                        ->label('Correo'),
                ]),

            Section::make('Detalle de la estancia')
                ->icon('heroicon-o-home')
                ->columns(2)
                ->schema([
                    TextEntry::make('reservation.room.number')
                        ->label('Habitación')
                        ->badge()
                        ->color('info'),

                    TextEntry::make('reservation.room.roomType.name')
                        ->label('Tipo de habitación'),

                    TextEntry::make('reservation.check_in_date')
                        ->label('Fecha entrada')
                        ->date('d/m/Y'),

                    TextEntry::make('reservation.check_out_date')
                        ->label('Fecha salida')
                        ->date('d/m/Y'),

                    TextEntry::make('reservation.nights')
                        ->label('Noches'),

                    TextEntry::make('reservation.rate')
                        ->label('Tarifa por noche')
                        ->money('COP'),
                ]),

            Section::make('Resumen de cobro')
                ->icon('heroicon-o-banknotes')
                ->columns(3)
                ->schema([
                    TextEntry::make('subtotal')
                        ->label('Subtotal')
                        ->money('COP'),

                    TextEntry::make('taxes')
                        ->label('Impuestos')
                        ->money('COP'),

                    TextEntry::make('total')
                        ->label('TOTAL')
                        ->money('COP')
                        ->weight('bold')
                        ->color('success'),
                ]),

        ]);
    }
}