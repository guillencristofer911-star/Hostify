<?php

namespace App\Filament\Resources\Reservations\Pages;

use App\Filament\Resources\Reservations\ReservationResource;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Reservation;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ViewReservation extends ViewRecord
{
    protected static string $resource = ReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [

            //  APROBAR — solo si pendiente
            Action::make('aprobar')
                ->label('Aprobar reserva')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Aprobar reserva')
                ->modalDescription('La reserva quedará confirmada y lista para registrar entrada.')
                ->visible(fn () => $this->record->status === 'pendiente')
                ->action(function () {
                    $this->record->update(['status' => 'aprobada']);
                    Notification::make()
                        ->title('Reserva aprobada')
                        ->success()->send();
                    $this->refreshFormData(['status']);
                }),

            //  RECHAZAR — solo si pendiente
            Action::make('rechazar')
                ->label('Rechazar')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->modalHeading('Rechazar reserva')
                ->form([
                    Textarea::make('rejection_reason')
                        ->label('Motivo de rechazo')
                        ->required()
                        ->rows(3),
                ])
                ->visible(fn () => $this->record->status === 'pendiente')
                ->action(function (array $data) {
                    $this->record->update([
                        'status'           => 'rechazada',
                        'rejection_reason' => $data['rejection_reason'],
                    ]);
                    Notification::make()
                        ->title('Reserva rechazada')
                        ->warning()->send();
                    $this->refreshFormData(['status']);
                }),
            //  REGISTRAR ENTRADA — solo si aprobada
            Action::make('checkin')
                ->label('Registrar entrada')
                ->icon('heroicon-o-arrow-right-on-rectangle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Registrar entrada')
                ->modalDescription(fn () =>
                    'Huésped: ' . $this->record->guest->full_name .
                    ' — Hab. ' . ($this->record->room?->number ?? 'Sin asignar')
                )
                ->visible(fn () => $this->record->status === 'aprobada')
                ->action(function () {
                    if (! $this->record->room) {
                        Notification::make()
                            ->title('Sin habitación asignada')
                            ->body('Edita la reserva y asigna una habitación primero.')
                            ->danger()->send();
                        return;
                    }
                    $this->record->update([
                        'status'          => 'activa',
                        'actual_check_in' => now(),
                    ]);
                    $this->record->room->updateStatus('ocupada');
                    Notification::make()
                        ->title('Entrada registrada')
                        ->body('Hab. ' . $this->record->room->number)
                        ->success()->send();
                    $this->refreshFormData(['status']);
                }),

            //  REGISTRAR SALIDA — solo si activa
            Action::make('checkout')
                ->label('Registrar salida')
                ->icon('heroicon-o-arrow-left-on-rectangle')
                ->color('warning')
                ->modalHeading('Registrar salida y cobro')
                ->modalDescription(fn () =>
                    'Huésped: ' . $this->record->guest->full_name .
                    ' — Hab. ' . $this->record->room?->number
                )
                ->form(function () {
                    $nights     = $this->record->nights;
                    $roomTotal  = $this->record->room_total;
                    $extraTotal = $this->record->total_charges;
                    $grandTotal = $this->record->invoice_total;

                    return [
                        Placeholder::make('resumen')
                            ->label('Resumen de estancia')
                            ->content(
                                "Noches: {$nights} × $" . number_format((float) $this->record->rate, 0, ',', '.') .
                                " = $" . number_format($roomTotal, 0, ',', '.') .
                                " | Extras: $" . number_format($extraTotal, 0, ',', '.') .
                                " | Total: $" . number_format($grandTotal, 0, ',', '.')
                            ),
                        TextInput::make('amount')
                            ->label('Monto recibido')
                            ->numeric()
                            ->prefix('$')
                            ->default($grandTotal)
                            ->required(),
                        Select::make('method')
                            ->label('Método de pago')
                            ->options([
                                'efectivo'      => 'Efectivo',
                                'datafono'      => 'Datáfono',
                                'transferencia' => 'Transferencia',
                            ])
                            ->default('efectivo')
                            ->required()
                            ->native(false),
                        Textarea::make('notes')
                            ->label('Observaciones')
                            ->rows(2)
                            ->placeholder('Opcional'),
                    ];
                })
                ->visible(fn () => $this->record->status === 'activa')
                ->action(function (array $data) {
                    DB::transaction(function () use ($data) {
                        $this->record->update([
                            'status'           => 'checked_out',
                            'actual_check_out' => now(),
                        ]);
                        $this->record->room->updateStatus('sucia');

                        $subtotal = $this->record->invoice_total;
                        Invoice::create([
                            'reservation_id' => $this->record->id,
                            'invoice_number' => Invoice::generateNumber(),
                            'subtotal'       => $subtotal,
                            'taxes'          => 0,
                            'total'          => $subtotal,
                            'status'         => 'pagada',
                        ]);
                        Payment::create([
                            'reservation_id' => $this->record->id,
                            'registered_by'  => Auth::id(),
                            'amount'         => $data['amount'],
                            'method'         => $data['method'],
                            'paid_at'        => now(),
                            'notes'          => $data['notes'] ?? null,
                        ]);
                    });
                    Notification::make()
                        ->title('Salida registrada — Factura generada')
                        ->body('Hab. ' . $this->record->room->number . ' queda pendiente de limpieza')
                        ->success()->send();
                    $this->refreshFormData(['status']);
                }),

            //  CANCELAR — solo si pendiente o aprobada
            Action::make('cancelar')
                ->label('Cancelar reserva')
                ->icon('heroicon-o-no-symbol')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Cancelar reserva')
                ->visible(fn () => in_array($this->record->status, ['pendiente', 'aprobada']))
                ->action(function () {
                    $this->record->update(['status' => 'cancelada']);
                    Notification::make()
                        ->title('Reserva cancelada')
                        ->danger()->send();
                    $this->refreshFormData(['status']);
                }),

            //  EDITAR — siempre visible excepto estados finales
            EditAction::make()
                ->label('Editar datos')
                ->icon('heroicon-o-pencil-square')
                ->visible(fn () => ! in_array($this->record->status, ['checked_out', 'rechazada', 'cancelada'])),
        ];
    }
}