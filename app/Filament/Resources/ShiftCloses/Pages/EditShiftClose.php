<?php

namespace App\Filament\Resources\ShiftCloses\Pages;

use App\Filament\Resources\ShiftCloses\ShiftCloseResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditShiftClose extends EditRecord
{
    protected static string $resource = ShiftCloseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
