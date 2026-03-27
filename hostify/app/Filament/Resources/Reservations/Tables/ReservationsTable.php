<?php

namespace App\Filament\Resources\Reservations\Tables;

use App\Models\Reservation;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

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
                    ->icon('heroicon-o-user'),

                TextColumn::make('guest.document_number')
                    ->label('Documento')
                    ->searchable()
                    ->icon('heroicon-o-identification'),

                TextColumn::make('room.number')
                    ->label('Habitación')
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-home'),

                TextColumn::make('check_in_date')
                    ->label('Entrada')
                    ->date('d/m/Y')
                    ->sortable()
                    ->icon('heroicon-o-arrow-right-circle'),

                TextColumn::make('check_out_date')
                    ->label('Salida')
                    ->date('d/m/Y')
                    ->sortable()
                    ->icon('heroicon-o-arrow-left-circle'),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pendiente'   => 'warning',
                        'aprobada'    => 'info',
                        'activa'      => 'success',
                        'checked_out' => 'gray',
                        'rechazada'   => 'danger',
                        'cancelada'   => 'danger',
                        default       => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'pendiente'   => 'heroicon-o-clock',
                        'aprobada'    => 'heroicon-o-check-circle',
                        'activa'      => 'heroicon-o-home',
                        'checked_out' => 'heroicon-o-arrow-left-on-rectangle',
                        'rechazada'   => 'heroicon-o-x-circle',
                        'cancelada'   => 'heroicon-o-no-symbol',
                        default       => 'heroicon-o-question-mark-circle',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pendiente'   => 'Pendiente',
                        'aprobada'    => 'Aprobada',
                        'activa'      => 'Activa',
                        'checked_out' => 'Check-out',
                        'rechazada'   => 'Rechazada',
                        'cancelada'   => 'Cancelada',
                        default       => $state,
                    }),

                TextColumn::make('rate')
                    ->label('Tarifa/noche')
                    ->money('COP')
                    ->icon('heroicon-o-banknotes'),

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
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pendiente'   => 'Pendiente',
                        'aprobada'    => 'Aprobada',
                        'activa'      => 'Activa',
                        'checked_out' => 'Check-out',
                        'rechazada'   => 'Rechazada',
                        'cancelada'   => 'Cancelada',
                    ]),

                SelectFilter::make('source')
                    ->label('Origen')
                    ->options([
                        'manual_reception' => 'Recepción',
                        'web_form'         => 'Web',
                    ]),
            ])
            ->recordActions([
                // APROBAR
                Action::make('aprobar')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Aprobar reserva')
                    ->modalIcon('heroicon-o-check-circle')
                    ->modalDescription('La reserva quedará confirmada y lista para registrar entrada.')
                    ->visible(fn (Reservation $record): bool => $record->status === 'pendiente')
                    ->action(function (Reservation $record) {
                        $record->update(['status' => 'aprobada']);
                        Notification::make()
                            ->title('Reserva aprobada')
                            ->icon('heroicon-o-check-circle')
                            ->success()
                            ->send();
                    }),

                // RECHAZAR
                Action::make('rechazar')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Rechazar reserva')
                    ->modalIcon('heroicon-o-x-circle')
                    ->form([
                        \Filament\Forms\Components\Textarea::make('rejection_reason')
                            ->label('Motivo de rechazo')
                            ->required()
                            ->rows(3),
                    ])
                    ->visible(fn (Reservation $record): bool => $record->status === 'pendiente')
                    ->action(function (Reservation $record, array $data) {
                        $record->update([
                            'status'           => 'rechazada',
                            'rejection_reason' => $data['rejection_reason'],
                        ]);
                        Notification::make()
                            ->title('Reserva rechazada')
                            ->icon('heroicon-o-x-circle')
                            ->warning()
                            ->send();
                    }),

                // REGISTRAR ENTRADA
                // REGISTRAR ENTRADA
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
    ->visible(fn (Reservation $record): bool => $record->status === 'aprobada')
    ->action(function (Reservation $record) {
        // Bloquear si no tiene habitación asignada
        if (! $record->room) {
            Notification::make()
                ->title('Sin habitación asignada')
                ->body('Debes editar la reserva y asignar una habitación antes de registrar la entrada.')
                ->icon('heroicon-o-exclamation-triangle')
                ->danger()
                ->send();
            return;
        }

        $record->update([
            'status'          => 'activa',
            'actual_check_in' => now(),
        ]);
        $record->room->updateStatus('ocupada');

        Notification::make()
            ->title('Entrada registrada')
            ->body("Hab. {$record->room->number} — {$record->guest->full_name}")
            ->icon('heroicon-o-arrow-right-on-rectangle')
            ->success()
            ->send();
    }),


                // REGISTRAR SALIDA
                Action::make('checkout')
                    ->label('Registrar salida')
                    ->icon('heroicon-o-arrow-left-on-rectangle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Registrar salida')
                    ->modalIcon('heroicon-o-arrow-left-on-rectangle')
                    ->modalDescription(fn (Reservation $record) =>
                        "¿Confirmar salida de {$record->guest->full_name} de Hab. {$record->room->number}?"
                    )
                    ->visible(fn (Reservation $record): bool => $record->status === 'activa')
                    ->action(function (Reservation $record) {
                        $record->update([
                            'status'           => 'checked_out',
                            'actual_check_out' => now(),
                        ]);
                        $record->room->updateStatus('sucia');

                        Notification::make()
                            ->title('Salida registrada')
                            ->body("Hab. {$record->room->number} queda pendiente de limpieza")
                            ->icon('heroicon-o-sparkles')
                            ->warning()
                            ->send();
                    }),

                // CANCELAR
                Action::make('cancelar')
                    ->label('Cancelar reserva')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Cancelar reserva')
                    ->modalIcon('heroicon-o-no-symbol')
                    ->visible(fn (Reservation $record): bool =>
                        in_array($record->status, ['pendiente', 'aprobada'])
                    )
                    ->action(function (Reservation $record) {
                        $record->update(['status' => 'cancelada']);
                        Notification::make()
                            ->title('Reserva cancelada')
                            ->icon('heroicon-o-no-symbol')
                            ->danger()
                            ->send();
                    }),

                EditAction::make()
                    ->label('Editar')
                    ->icon('heroicon-o-pencil-square'),

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
