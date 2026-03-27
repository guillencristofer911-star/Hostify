<?php

namespace App\Filament\Resources\Reservations\Pages;

use App\Filament\Resources\Reservations\ReservationResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateReservation extends CreateRecord
{
    protected static string $resource = ReservationResource::class;

    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Guardar reserva')
            ->icon('heroicon-o-check-circle');
    }

    protected function getCreateAnotherFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateAnotherFormAction()
            ->label('Guardar y crear otra')
            ->icon('heroicon-o-plus-circle');
    }

    protected function getCancelFormAction(): \Filament\Actions\Action
    {
        return parent::getCancelFormAction()
            ->label('Cancelar')
            ->icon('heroicon-o-x-mark');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status']     = 'aprobada';
        $data['source']     = 'manual_reception';
        $data['created_by'] = Auth::id();

        return $data;
    }
}
