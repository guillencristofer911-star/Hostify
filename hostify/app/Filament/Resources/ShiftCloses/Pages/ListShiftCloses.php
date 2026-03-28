<?php

namespace App\Filament\Resources\ShiftCloses\Pages;

use App\Filament\Resources\ShiftCloses\ShiftCloseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListShiftCloses extends ListRecords
{
    protected static string $resource = ShiftCloseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Abrir turno')
                ->icon('heroicon-o-play'),
        ];
    }
}