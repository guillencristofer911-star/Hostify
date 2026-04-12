<?php

namespace App\Filament\Resources\CleaningSessions\Pages;

use App\Enums\CleaningStatus;
use App\Enums\RoomStatus;
use App\Filament\Resources\CleaningSessions\CleaningSessionResource;
use App\Models\CleaningSession;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ViewCleaningSession extends ViewRecord
{
    protected static string $resource = CleaningSessionResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Ver Sesión de Limpieza';
    }

    public function getHeading(): string|Htmlable
    {
        return 'Ver Sesión de Limpieza';
    }

    public function getBreadcrumb(): string
    {
        return 'Ver';
    }

    protected function getHeaderActions(): array
    {
        /** @var CleaningSession $record */
        $record = $this->record;

        return [

            Action::make('iniciar')
                ->label('Iniciar limpieza')
                ->icon('heroicon-o-play')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Iniciar limpieza')
                ->modalDescription('Se registrará la hora de inicio ahora.')
                ->modalSubmitActionLabel('Sí, iniciar')
                ->modalCancelActionLabel('Cancelar')
                ->visible(fn () => $record->status === CleaningStatus::Pendiente)
                ->action(function () use ($record): void {
                    $record->update([
                        'status'     => CleaningStatus::EnProceso->value,
                        'started_at' => now(),
                    ]);

                    $record->room?->updateStatus(
                        RoomStatus::Sucia,
                        Auth::id(),
                        'system'
                    );

                    Notification::make()
                        ->title('Limpieza iniciada')
                        ->body("Hab. {$record->room?->number}")
                        ->warning()
                        ->send();

                    $this->refreshFormData(['status', 'started_at']);
                }),

            Action::make('terminar')
                ->label('Marcar terminada')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Marcar limpieza como terminada')
                ->modalDescription('Se registrará la hora de fin y la habitación quedará libre.')
                ->modalSubmitActionLabel('Sí, terminar')
                ->modalCancelActionLabel('Cancelar')
                ->visible(fn () => $record->status === CleaningStatus::EnProceso)
                ->action(function () use ($record): void {
                    $finishedAt = now();
                    $duration   = $record->started_at
                        ? (int) Carbon::parse($record->started_at)->diffInMinutes($finishedAt)
                        : null;

                    $record->update([
                        'status'           => CleaningStatus::Terminada->value,
                        'finished_at'      => $finishedAt,
                        'duration_minutes' => $duration,
                    ]);

                    $record->room?->updateStatus(
                        RoomStatus::Libre,
                        Auth::id(),
                        'system'
                    );

                    Notification::make()
                        ->title('Limpieza terminada')
                        ->body("Hab. {$record->room?->number} — {$duration} min")
                        ->success()
                        ->send();

                    $this->refreshFormData(['status', 'finished_at', 'duration_minutes']);
                }),

            EditAction::make()
                ->label('Editar')
                ->icon('heroicon-o-pencil-square'),
        ];
    }
}