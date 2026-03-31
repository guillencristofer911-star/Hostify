<?php

namespace App\Filament\Resources\CleaningSessions\Tables;

use App\Enums\CleaningStatus;
use App\Enums\RoomStatus;
use App\Models\CleaningSession;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class CleaningSessionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('room.number')
                    ->label('Hab.')
                    ->sortable()
                    ->searchable()
                    ->weight('bold')
                    ->badge()
                    ->color('info'),

                TextColumn::make('assignedTo.name')
                    ->label('Camarera')
                    ->sortable()
                    ->searchable()
                    ->icon('heroicon-o-user'),

                TextColumn::make('assigned_date')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar'),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof CleaningStatus
                        ? $state->label()
                        : $state
                    )
                    ->color(fn ($state) => $state instanceof CleaningStatus
                        ? $state->color()
                        : 'gray'
                    )
                    ->sortable(),

                TextColumn::make('started_at')
                    ->label('Inicio')
                    ->time('H:i')
                    ->placeholder('—')
                    ->icon('heroicon-o-play'),

                TextColumn::make('finished_at')
                    ->label('Fin')
                    ->time('H:i')
                    ->placeholder('—')
                    ->icon('heroicon-o-stop'),

                TextColumn::make('duration_minutes')
                    ->label('Duración')
                    ->suffix(' min')
                    ->placeholder('—')
                    ->alignCenter(),

                ImageColumn::make('photo_after_url')
                    ->label('Foto')
                    ->circular()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options(CleaningStatus::options()),

                SelectFilter::make('assigned_to')
                    ->label('Camarera')
                    ->relationship('assignedTo', 'name'),
            ])

            ->recordActions([
                ViewAction::make(),

                Action::make('iniciar')
                    ->label('Iniciar')
                    ->icon('heroicon-o-play')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Iniciar limpieza')
                    ->modalDescription('Se registrará la hora de inicio ahora.')
                    ->modalSubmitActionLabel('Sí, iniciar')
                    ->modalCancelActionLabel('Cancelar')
                    ->visible(fn (CleaningSession $record): bool =>
                        $record->status === CleaningStatus::Pendiente
                    )
                    ->action(function (CleaningSession $record): void {
                        $record->update([
                            'status'     => CleaningStatus::EnProceso->value,
                            'started_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Limpieza iniciada')
                            ->body("Hab. {$record->room?->number}")
                            ->icon('heroicon-o-play')
                            ->warning()
                            ->send();
                    }),

                Action::make('terminar')
                    ->label('Terminar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Marcar como terminada')
                    ->modalDescription('Se registrará la hora de fin y la habitación quedará libre.')
                    ->modalSubmitActionLabel('Sí, terminar')
                    ->modalCancelActionLabel('Cancelar')
                    ->visible(fn (CleaningSession $record): bool =>
                        $record->status === CleaningStatus::EnProceso
                    )
                    ->action(function (CleaningSession $record): void {
                        $finishedAt = now();
                        $duration   = $record->started_at
                            ? (int) Carbon::parse($record->started_at)->diffInMinutes($finishedAt)
                            : null;

                        $record->update([
                            'status'           => CleaningStatus::Terminada->value,
                            'finished_at'      => $finishedAt,
                            'duration_minutes' => $duration,
                        ]);

                        $record->room?->updateStatus(RoomStatus::Libre);

                        Notification::make()
                            ->title('Limpieza terminada')
                            ->body("Hab. {$record->room?->number} — {$duration} min")
                            ->icon('heroicon-o-check-circle')
                            ->success()
                            ->send();
                    }),

                EditAction::make(),

                DeleteAction::make()
                    ->modalHeading('Eliminar sesión')
                    ->modalDescription('Esta acción no se puede deshacer.')
                    ->modalSubmitActionLabel('Sí, eliminar')
                    ->modalCancelActionLabel('Cancelar'),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Eliminar seleccionadas')
                        ->modalHeading('Eliminar sesiones seleccionadas')
                        ->modalDescription('Esta acción no se puede deshacer.')
                        ->modalSubmitActionLabel('Sí, eliminar')
                        ->modalCancelActionLabel('Cancelar'),
                ]),
            ])

            ->defaultSort('assigned_date', 'desc')
            ->striped()
            ->poll('30s');
    }
}