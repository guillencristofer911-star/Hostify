<?php

namespace App\Filament\Resources\CleaningSessions\Pages;

use App\Enums\CleaningStatus;
use App\Filament\Resources\CleaningSessions\CleaningSessionResource;
use App\Models\CleaningSession;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListCleaningSessions extends ListRecords
{
    protected static string $resource = CleaningSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nueva asignación')
                ->icon('heroicon-o-plus-circle'),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'todas';
    }

    public function getTabs(): array
    {
        return [
            'todas' => Tab::make('Todas')
                ->badge(static fn () => CleaningSession::count())
                ->deferBadge(),

            'pendientes' => Tab::make('Pendientes')
                ->icon('heroicon-o-clock')
                ->badge(static fn () => CleaningSession::where('status', CleaningStatus::Pendiente->value)->count())
                ->badgeColor('warning')
                ->deferBadge()
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('status', CleaningStatus::Pendiente->value)
                          ->orderBy('assigned_date', 'asc')
                ),

            'en_proceso' => Tab::make('En proceso')
                ->icon('heroicon-o-sparkles')
                ->badge(static fn () => CleaningSession::where('status', CleaningStatus::EnProceso->value)->count())
                ->badgeColor('info')
                ->deferBadge()
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('status', CleaningStatus::EnProceso->value)
                          ->orderBy('started_at', 'asc')
                ),

            'terminadas' => Tab::make('Terminadas hoy')
                ->icon('heroicon-o-check-circle')
                ->badge(static fn () => CleaningSession::where('status', CleaningStatus::Terminada->value)
                    ->whereDate('assigned_date', today())
                    ->count()
                )
                ->badgeColor('success')
                ->deferBadge()
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('status', CleaningStatus::Terminada->value)
                          ->orderBy('finished_at', 'desc')
                ),

            'historial' => Tab::make('Historial')
                ->icon('heroicon-o-archive-box')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->whereDate('assigned_date', '<', today())
                          ->orderBy('assigned_date', 'desc')
                ),
        ];
    }
}