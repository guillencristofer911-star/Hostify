<?php

namespace App\Filament\Resources\Reservations\Pages;

use App\Enums\PaymentMethod;
use App\Enums\ReservationStatus;
use App\Filament\Resources\Reservations\ReservationResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewReservation extends ViewRecord
{
    protected static string $resource = ReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [

            // APROBAR
            Action::make('aprobar')
                ->label('Aprobar reserva')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Aprobar reserva')
                ->modalDescription('La reserva quedará confirmada y lista para registrar entrada.')
                ->modalSubmitActionLabel('Sí, aprobar')
                ->modalCancelActionLabel('Cancelar')
                ->visible(fn () => $this->record->status === ReservationStatus::Pendiente)
                ->action(function () {
                    try {
                        $this->record->approve();
                        Notification::make()
                            ->title('Reserva aprobada')
                            ->success()
                            ->send();
                        $this->refreshFormData(['status']);
                    } catch (\DomainException $e) {
                        Notification::make()
                            ->title($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            // RECHAZAR
            Action::make('rechazar')
                ->label('Rechazar')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->modalHeading('Rechazar reserva')
                ->modalSubmitActionLabel('Sí, rechazar')
                ->modalCancelActionLabel('Cancelar')
                ->form([
                    Textarea::make('rejection_reason')
                        ->label('Motivo de rechazo')
                        ->required()
                        ->rows(3),
                ])
                ->visible(fn () => $this->record->status === ReservationStatus::Pendiente)
                ->action(function (array $data) {
                    try {
                        $this->record->reject($data['rejection_reason']);
                        Notification::make()
                            ->title('Reserva rechazada')
                            ->warning()
                            ->send();
                        $this->refreshFormData(['status']);
                    } catch (\DomainException $e) {
                        Notification::make()
                            ->title($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            // REGISTRAR ENTRADA
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
                ->modalSubmitActionLabel('Sí, registrar entrada')
                ->modalCancelActionLabel('Cancelar')
                ->visible(fn () => $this->record->status === ReservationStatus::Aprobada)
                ->action(function () {
                    try {
                        $this->record->checkin();
                        Notification::make()
                            ->title('Entrada registrada')
                            ->body('Hab. ' . $this->record->room->number)
                            ->success()
                            ->send();
                        $this->refreshFormData(['status']);
                    } catch (\DomainException $e) {
                        Notification::make()
                            ->title($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            // REGISTRAR SALIDA
            Action::make('checkout')
                ->label('Registrar salida')
                ->icon('heroicon-o-arrow-left-on-rectangle')
                ->color('warning')
                ->modalHeading('Registrar salida y cobro')
                ->modalSubmitActionLabel('Confirmar salida y cobro')
                ->modalCancelActionLabel('Cancelar')
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
                                " = $"  . number_format($roomTotal,  0, ',', '.') .
                                " | Extras: $" . number_format($extraTotal, 0, ',', '.') .
                                " | Total: $"  . number_format($grandTotal, 0, ',', '.')
                            ),

                        TextInput::make('amount')
                            ->label('Monto recibido')
                            ->numeric()
                            ->prefix('$')
                            ->default($grandTotal)
                            ->required(),

                        Select::make('method')
                            ->label('Método de pago')
                            ->options(PaymentMethod::options())
                            ->default(PaymentMethod::Efectivo->value)
                            ->required()
                            ->native(false),

                        Textarea::make('notes')
                            ->label('Observaciones')
                            ->rows(2)
                            ->placeholder('Opcional'),
                    ];
                })
                ->visible(fn () => $this->record->status === ReservationStatus::Activa)
                ->action(function (array $data) {
                    try {
                        $this->record->checkout(
                            amount: (float) $data['amount'],
                            method: $data['method'],
                            notes:  $data['notes'] ?? null,
                        );
                        Notification::make()
                            ->title('Salida registrada — Factura generada')
                            ->body('Hab. ' . $this->record->room->number . ' queda pendiente de limpieza')
                            ->success()
                            ->send();
                        $this->refreshFormData(['status']);
                    } catch (\DomainException $e) {
                        Notification::make()
                            ->title($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            // CANCELAR
            Action::make('cancelar')
                ->label('Cancelar reserva')
                ->icon('heroicon-o-no-symbol')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Cancelar reserva')
                ->modalDescription('Esta acción no se puede deshacer.')
                ->modalSubmitActionLabel('Sí, cancelar reserva')
                ->modalCancelActionLabel('Volver')
                ->visible(fn () => in_array($this->record->status, [
                    ReservationStatus::Pendiente,
                    ReservationStatus::Aprobada,
                ]))
                ->action(function () {
                    try {
                        $this->record->cancel();
                        Notification::make()
                            ->title('Reserva cancelada')
                            ->danger()
                            ->send();
                        $this->refreshFormData(['status']);
                    } catch (\DomainException $e) {
                        Notification::make()
                            ->title($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            // EDITAR
            EditAction::make()
                ->label('Editar datos')
                ->icon('heroicon-o-pencil-square')
                ->visible(fn () => ! $this->record->status->isFinal()),
        ];
    }
}