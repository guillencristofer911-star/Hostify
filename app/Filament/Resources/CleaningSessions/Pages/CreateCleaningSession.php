<?php

namespace App\Filament\Resources\CleaningSessions\Pages;

use App\Enums\CleaningStatus;
use App\Filament\Resources\CleaningSessions\CleaningSessionResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateCleaningSession extends CreateRecord
{
    protected static string $resource = CleaningSessionResource::class;

    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Guardar asignación')
            ->icon('heroicon-o-check-circle');
    }

    protected function getCreateAnotherFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateAnotherFormAction()
            ->label('Guardar y asignar otra')
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
        $data['assigned_by'] = Auth::id();
        $data['status']    ??= CleaningStatus::Pendiente->value;

        return $data;
    }
}