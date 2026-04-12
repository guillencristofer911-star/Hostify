<?php

namespace App\Filament\Resources\RoomTypes\Pages;

use App\Filament\Resources\RoomTypes\RoomTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditRoomType extends EditRecord
{
    protected static string $resource = RoomTypeResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Editar Tipo de Habitación';
    }

    public function getHeading(): string|Htmlable
    {
        return 'Editar Tipo de Habitación';
    }

    public function getBreadcrumb(): string
    {
        return 'Editar';
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Eliminar')
                ->icon('heroicon-o-trash')
                ->modalHeading('Eliminar tipo de habitación')
                ->modalDescription('¿Estás seguro de que deseas eliminar este tipo de habitación?')
                ->modalSubmitActionLabel('Eliminar')
                ->modalCancelActionLabel('Cancelar'),

            ForceDeleteAction::make()
                ->label('Eliminar permanentemente')
                ->icon('heroicon-o-exclamation-triangle')
                ->modalHeading('Eliminar permanentemente')
                ->modalDescription('¿Estás seguro? Esta acción es irreversible.')
                ->modalSubmitActionLabel('Eliminar permanentemente')
                ->modalCancelActionLabel('Cancelar'),

            RestoreAction::make()
                ->label('Restaurar')
                ->icon('heroicon-o-arrow-uturn-left')
                ->modalHeading('Restaurar tipo de habitación')
                ->modalDescription('¿Deseas restaurar este tipo de habitación?')
                ->modalSubmitActionLabel('Restaurar')
                ->modalCancelActionLabel('Cancelar'),
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