<?php

namespace App\Filament\Resources\Reservations\Tables;

use App\Models\Reservation;
use App\Models\Room;
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
                    ->weight('bold'),

                TextColumn::make('guest.document_number')
                    ->label('Documento')
                    ->searchable(),

                TextColumn::make('room.number')
                    ->label('Habitación')
                    ->badge()
                    ->color('info'),

                TextColumn::make('check_in_date')
                    ->label('Entrada')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('check_out_date')
                    ->label('Salida')
                    ->date('d/m/Y')
                    ->sortable(),

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
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pendiente'   => '🕐 Pendiente',
                        'aprobada'    => '✅ Aprobada',
                        'activa'      => '🏠 Activa',
                        'checked_out' => '🚪 Check-out',
                        'rechazada'   => '❌ Rechazada',
                        'cancelada'   => '🚫 Cancelada',
                        default       => $state,
                    }),

                TextColumn::make('rate')
                    ->label('Tarifa/noche')
                    ->money('COP'),

                TextColumn::make('source')
                    ->label('Origen')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'manual_reception' => '🖥️ Recepción',
                        'web_form'         => '🌐 Web',
                        default            => $state,
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pendiente'   => '🕐 Pendiente',
                        'aprobada'    => '✅ Aprobada',
                        'activa'      => '🏠 Activa',
                        'checked_out' => '🚪 Check-out',
                        'rechazada'   => '❌ Rechazada',
                        'cancelada'   => '🚫 Cancelada',
                    ]),

                SelectFilter::make('source')
                    ->label('Origen')
                    ->options([
                        'manual_reception' => '🖥️ Recepción',
                        'web_form'         => '🌐 Web',
                    ]),
            ])
            ->recordActions([
                //  APROBAR 
                Action::make('aprobar')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('¿Aprobar reserva?')
                    ->modalDescription('La reserva quedará confirmada y lista para check-in.')
                    ->visible(fn (Reservation $record): bool => $record->status === 'pendiente')
                    ->action(function (Reservation $record) {
                        $record->update(['status' => 'aprobada']);
                        Notification::make()
                            ->title('Reserva aprobada')
                            ->success()
                            ->send();
                    }),

                // RECHAZAR 
                Action::make('rechazar')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('¿Rechazar reserva?')
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
                            ->warning()
                            ->send();
                    }),

                //  CHECK-IN 
                Action::make('checkin')
                    ->label('Check-in')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Confirmar Check-in')
                    ->modalDescription(fn (Reservation $record) =>
                        "Huésped: {$record->guest->full_name} — Hab. {$record->room->number}"
                    )
                    ->visible(fn (Reservation $record): bool => $record->status === 'aprobada')
                    ->action(function (Reservation $record) {
                        $record->update([
                            'status'          => 'activa',
                            'actual_check_in' => now(),
                        ]);
                        $record->room->updateStatus('ocupada');

                        Notification::make()
                            ->title('Check-in realizado')
                            ->body("Hab. {$record->room->number} — {$record->guest->full_name}")
                            ->success()
                            ->send();
                    }),

                //  CHECK-OUT 
                Action::make('checkout')
                    ->label('Check-out')
                    ->icon('heroicon-o-arrow-left-circle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Confirmar Check-out')
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
                            ->title('Check-out realizado')
                            ->body("Hab. {$record->room->number} queda pendiente de limpieza 🧹")
                            ->warning()
                            ->send();
                    }),

                //  CANCELAR 
                Action::make('cancelar')
                    ->label('Cancelar')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('¿Cancelar reserva?')
                    ->visible(fn (Reservation $record): bool =>
                        in_array($record->status, ['pendiente', 'aprobada'])
                    )
                    ->action(function (Reservation $record) {
                        $record->update(['status' => 'cancelada']);
                        Notification::make()
                            ->title('Reserva cancelada')
                            ->danger()
                            ->send();
                    }),

                EditAction::make()->label('Editar'),
                DeleteAction::make()->label('Eliminar'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
