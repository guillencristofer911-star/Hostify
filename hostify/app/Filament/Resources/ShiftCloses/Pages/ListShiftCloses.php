<?php

namespace App\Filament\Resources\ShiftCloses\Pages;

use App\Enums\ShiftCloseStatus;
use App\Filament\Resources\ShiftCloses\ShiftCloseResource;
use App\Models\ShiftClose;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

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

    public function getDefaultActiveTab(): string|int|null
    {
        return 'todos';
    }

    public function getTabs(): array
    {
        return [
            'todos' => Tab::make('Todos')
                ->icon('heroicon-o-queue-list')
                ->badge(static fn () => ShiftClose::count())
                ->deferBadge(),

            'abierto' => Tab::make('Abiertos')
                ->icon('heroicon-o-play-circle')
                //  Enum en badge count
                ->badge(static fn () => ShiftClose::where('status', ShiftCloseStatus::Abierto)->count())
                ->badgeColor(ShiftCloseStatus::Abierto->color())   //  color del Enum
                ->deferBadge()
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('status', ShiftCloseStatus::Abierto)  //  Enum
                          ->orderBy('shift_start', 'desc')
                ),

            'cerrado' => Tab::make('Cerrados')
                ->icon('heroicon-o-stop-circle')
                //  Enum en badge count
                ->badge(static fn () => ShiftClose::where('status', ShiftCloseStatus::Cerrado)->count())
                ->badgeColor(ShiftCloseStatus::Cerrado->color())   //  color del Enum
                ->deferBadge()
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('status', ShiftCloseStatus::Cerrado)  //  Enum
                          ->orderBy('shift_end', 'desc')
                ),

            'validado' => Tab::make('Validados')
                ->icon('heroicon-o-check-badge')
                //  Enum en badge count
                ->badge(static fn () => ShiftClose::where('status', ShiftCloseStatus::Validado)->count())
                ->badgeColor(ShiftCloseStatus::Validado->color())  
                ->deferBadge()
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('status', ShiftCloseStatus::Validado)  
                          ->orderBy('validated_at', 'desc')
                ),
        ];
    }
}