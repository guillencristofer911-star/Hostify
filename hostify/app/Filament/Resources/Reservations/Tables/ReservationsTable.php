<?php

namespace App\Filament\Resources\Reservations\Tables;

use App\Enums\ReservationStatus;
use App\Filament\Resources\Reservations\ReservationResource;
use App\Models\Reservation;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ReservationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('guest.full_name')
                    ->label('Huésped')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-user')
                    ->toggleable(),

                TextColumn::make('guest.document_number')
                    ->label('Documento')
                    ->searchable()
                    ->icon('heroicon-o-identification')
                    ->toggleable(),

                TextColumn::make('room.number')
                    ->label('Habitación')
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-home')
                    ->toggleable(),

                TextColumn::make('check_in_date')
                    ->label('Entrada')
                    ->date('d/m/Y')
                    ->sortable()
                    ->icon('heroicon-o-arrow-right-circle')
                    ->toggleable(),

                TextColumn::make('check_out_date')
                    ->label('Salida')
                    ->date('d/m/Y')
                    ->sortable()
                    ->icon('heroicon-o-arrow-left-circle')
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (ReservationStatus $state): string => $state->color())
                    ->icon(fn (ReservationStatus $state): string => $state->icon())
                    ->formatStateUsing(fn (ReservationStatus $state): string => $state->label())
                    ->toggleable(),

                TextColumn::make('rate')
                    ->label('Tarifa/noche')
                    ->money('COP')
                    ->icon('heroicon-o-banknotes')
                    ->toggleable(),

                TextColumn::make('source')
                    ->label('Origen')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'manual_reception' => 'gray',
                        'web_form'         => 'info',
                        default            => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'manual_reception' => 'heroicon-o-computer-desktop',
                        'web_form'         => 'heroicon-o-globe-alt',
                        default            => 'heroicon-o-question-mark-circle',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'manual_reception' => 'Recepción',
                        'web_form'         => 'Web',
                        default            => $state,
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->icon('heroicon-o-calendar')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordUrl(
                fn (Reservation $record) => ReservationResource::getUrl('view', ['record' => $record])
            )
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options(ReservationStatus::options()),

                SelectFilter::make('source')
                    ->label('Origen')
                    ->options([
                        'manual_reception' => 'Recepción',
                        'web_form'         => 'Web',
                    ]),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Ver detalle')
                    ->icon('heroicon-o-eye'),

                Action::make('aprobar')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Aprobar reserva')
                    ->modalIcon('heroicon-o-check-circle')
                    ->modalDescription('La reserva quedará confirmada y lista para registrar entrada.')
                    ->visible(fn (Reservation $record): bool => $record->status === ReservationStatus::Pendiente)
                    ->action(function (Reservation $record) {
                        $record->approve();

                        Notification::make()
                            ->title('Reserva aprobada')
                            ->icon('heroicon-o-check-circle')
                            ->success()
                            ->send();
                    }),

                Action::make('rechazar')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Rechazar reserva')
                    ->modalIcon('heroicon-o-x-circle')
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label('Motivo de rechazo')
                            ->required()
                            ->rows(3),
                    ])
                    ->visible(fn (Reservation $record): bool => $record->status === ReservationStatus::Pendiente)
                    ->action(function (Reservation $record, array $data) {
                        $record->reject($data['rejection_reason']);

                        Notification::make()
                            ->title('Reserva rechazada')
                            ->icon('heroicon-o-x-circle')
                            ->warning()
                            ->send();
                    }),

                Action::make('checkin')
                    ->label('Registrar entrada')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Registrar entrada')
                    ->modalIcon('heroicon-o-arrow-right-on-rectangle')
                    ->modalDescription(fn (Reservation $record) =>
                        "Huésped: {$record->guest->full_name} — Hab. " . ($record->room?->number ?? 'Sin asignar')
                    )
                    ->visible(fn (Reservation $record): bool => $record->status === ReservationStatus::Aprobada)
                    ->action(function (Reservation $record) {
                        try {
                            $record->checkin();

                            Notification::make()
                                ->title('Entrada registrada')
                                ->body("Hab. {$record->room->number} — {$record->guest->full_name}")
                                ->icon('heroicon-o-arrow-right-on-rectangle')
                                ->success()
                                ->send();
                        } catch (\DomainException $e) {
                            Notification::make()
                                ->title('No se pudo registrar la entrada')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Action::make('checkout')
                    ->label('Registrar salida')
                    ->icon('heroicon-o-arrow-left-on-rectangle')
                    ->color('warning')
                    ->modalHeading('Registrar salida y cobro')
                    ->modalIcon('heroicon-o-arrow-left-on-rectangle')
                    ->modalDescription(fn (Reservation $record) =>
                        "Huésped: {$record->guest->full_name} — Hab. {$record->room?->number}"
                    )
                    ->form(function (Reservation $record) {
                        $nights     = $record->nights;
                        $roomTotal  = $record->room_total;
                        $extraTotal = $record->total_charges;
                        $grandTotal = $record->invoice_total;

                        return [
                            Placeholder::make('resumen')
                                ->label('Resumen de estancia')
                                ->content(
                                    "Noches: {$nights} × $" . number_format((float) $record->rate, 0, ',', '.') .
                                    " = $" . number_format($roomTotal, 0, ',', '.') .
                                    " | Cargos extras: $" . number_format($extraTotal, 0, ',', '.') .
                                    " | Total: $" . number_format($grandTotal, 0, ',', '.')
                                ),

                            TextInput::make('amount')
                                ->label('Monto recibido')
                                ->numeric()
                                ->prefix('$')
                                ->default(fn () => $grandTotal)
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
                    ->visible(fn (Reservation $record): bool => $record->status === ReservationStatus::Activa)
                    ->action(function (Reservation $record, array $data) {
                        try {
                            $record->checkout(
                                amount: (float) $data['amount'],
                                method: $data['method'],
                                notes:  $data['notes'] ?? null,
                            );

                            Notification::make()
                                ->title('Salida registrada — Factura generada')
                                ->body("Hab. {$record->room->number} queda pendiente de limpieza")
                                ->icon('heroicon-o-document-check')
                                ->success()
                                ->send();
                        } catch (\DomainException $e) {
                            Notification::make()
                                ->title('No se pudo registrar la salida')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Action::make('cancelar')
                    ->label('Cancelar reserva')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Cancelar reserva')
                    ->modalIcon('heroicon-o-no-symbol')
                    ->visible(fn (Reservation $record): bool =>
                        in_array($record->status, [ReservationStatus::Pendiente, ReservationStatus::Aprobada])
                    )
                    ->action(function (Reservation $record) {
                        $record->cancel();

                        Notification::make()
                            ->title('Reserva cancelada')
                            ->icon('heroicon-o-no-symbol')
                            ->danger()
                            ->send();
                    }),

                DeleteAction::make()
                    ->label('Eliminar')
                    ->icon('heroicon-o-trash'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Eliminar seleccionados'),
                ]),
            ]);
    }
}