<?php

namespace App\Filament\Resources\ShiftCloses\Pages;

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
                ->badge(static fn () => ShiftClose::where('status', 'abierto')->count())
                ->badgeColor('success')
                ->deferBadge()
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('status', 'abierto')
                          ->orderBy('shift_start', 'desc')
                ),

            'cerrado' => Tab::make('Cerrados')
                ->icon('heroicon-o-stop-circle')
                ->badge(static fn () => ShiftClose::where('status', 'cerrado')->count())
                ->badgeColor('warning')
                ->deferBadge()
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('status', 'cerrado')
                          ->orderBy('shift_end', 'desc')
                ),

            'validado' => Tab::make('Validados')
                ->icon('heroicon-o-check-badge')
                ->badge(static fn () => ShiftClose::where('status', 'validado')->count())
                ->badgeColor('info')
                ->deferBadge()
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('status', 'validado')
                          ->orderBy('validated_at', 'desc')
                ),
        ];
    }
}