<?php

namespace App\Filament\Resources\CleaningSessions\Pages;

use App\Filament\Resources\CleaningSessions\CleaningSessionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditCleaningSession extends EditRecord
{
    protected static string $resource = CleaningSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Eliminar')
                ->icon('heroicon-o-trash'),

            ForceDeleteAction::make()
                ->label('Eliminar permanentemente')
                ->icon('heroicon-o-exclamation-triangle'),

            RestoreAction::make()
                ->label('Restaurar')
                ->icon('heroicon-o-arrow-uturn-left'),
        ];
    }

    protected function getSaveFormAction(): \Filament\Actions\Action
    {
        return parent::getSaveFormAction()
            ->label('Guardar cambios')
            ->icon('heroicon-o-check-circle');
    }

    protected function getCancelFormAction(): \Filament\Actions\Action
    {
        return parent::getCancelFormAction()
            ->label('Cancelar')
            ->icon('heroicon-o-x-mark');
    }
}