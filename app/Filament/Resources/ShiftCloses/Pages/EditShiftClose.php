<?php

namespace App\Filament\Resources\ShiftCloses\Pages;

use App\Filament\Resources\ShiftCloses\ShiftCloseResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditShiftClose extends EditRecord
{
    protected static string $resource = ShiftCloseResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Editar Cierre de Turno';
    }

    public function getHeading(): string|Htmlable
    {
        return 'Editar Cierre de Turno';
    }

    public function getBreadcrumb(): string
    {
        return 'Editar';
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('Ver'),

            DeleteAction::make()
                ->label('Eliminar')
                ->icon('heroicon-o-trash')
                ->modalHeading('Eliminar cierre de turno')
                ->modalDescription('¿Estás seguro de que deseas eliminar este cierre de turno? Esta acción no se puede deshacer.')
                ->modalSubmitActionLabel('Eliminar')
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