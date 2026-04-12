<?php

namespace App\Filament\Resources\ShiftCloses\Pages;

use App\Enums\ShiftCloseStatus;
use App\Filament\Resources\ShiftCloses\ShiftCloseResource;
use App\Models\ShiftClose;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;

class CreateShiftClose extends CreateRecord
{
    protected static string $resource = ShiftCloseResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Crear Cierre de Turno';
    }

    public function getHeading(): string|Htmlable
    {
        return 'Crear Cierre de Turno';
    }

    public function getBreadcrumb(): string
    {
        return 'Crear';
    }

    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Abrir turno')
            ->icon('heroicon-o-play')
            ->requiresConfirmation()
            ->modalHeading('Abrir turno')
            ->modalDescription('Se registrará el inicio del turno con la hora actual.')
            ->modalSubmitActionLabel('Confirmar')
            ->modalCancelActionLabel('Cancelar');
    }

    protected function getCreateAnotherFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateAnotherFormAction()->hidden();
    }

    protected function getCancelFormAction(): \Filament\Actions\Action
    {
        return parent::getCancelFormAction()
            ->label('Cancelar')
            ->icon('heroicon-o-x-mark');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $hayTurnoAbierto = ShiftClose::where('status', ShiftCloseStatus::Abierto)->exists();

        if ($hayTurnoAbierto) {
            Notification::make()
                ->title('Ya hay un turno abierto')
                ->body('Cierra el turno actual antes de abrir uno nuevo.')
                ->warning()
                ->send();

            $this->halt();
        }

        $data['opened_by']   = Auth::id();
        $data['shift_start'] = now();
        $data['status']      = ShiftCloseStatus::Abierto->value;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return ShiftCloseResource::getUrl('index');
    }
}