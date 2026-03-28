<?php

namespace App\Filament\Resources\Reservations\Pages;

use App\Enums\ReservationStatus;
use App\Filament\Resources\Reservations\ReservationResource;
use App\Models\Reservation;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListReservations extends ListRecords
{
    protected static string $resource = ReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nueva Reserva')
                ->icon('heroicon-o-plus-circle'),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'todas';
    }

    public function getTabs(): array
    {
        $hoy = now()->toDateString();

        return [
            'todas' => Tab::make('Todas')
                ->badge(static fn () => Reservation::count())
                ->deferBadge(),

            'activas' => Tab::make('Activas')
                ->icon('heroicon-o-home')
                ->badge(static fn () => Reservation::where('status', ReservationStatus::Activa)->count())
                ->badgeColor('success')
                ->deferBadge()
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('status', ReservationStatus::Activa)
                          ->orderBy('check_out_date', 'asc')
                ),

            'entrando_hoy' => Tab::make('Entrando hoy')
                ->icon('heroicon-o-arrow-right-end-on-rectangle')
                ->badge(static fn () => Reservation::where('status', ReservationStatus::Aprobada)
                    ->whereDate('check_in_date', now()->toDateString())
                    ->count()
                )
                ->badgeColor('warning')
                ->deferBadge()
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('status', ReservationStatus::Aprobada)
                          ->whereDate('check_in_date', $hoy)
                          ->orderBy('check_in_date', 'asc')
                ),

            'saliendo_hoy' => Tab::make('Saliendo hoy')
                ->icon('heroicon-o-arrow-left-end-on-rectangle')
                ->badge(static fn () => Reservation::where('status', ReservationStatus::Activa)
                    ->whereDate('check_out_date', now()->toDateString())
                    ->count()
                )
                ->badgeColor('danger')
                ->deferBadge()
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('status', ReservationStatus::Activa)
                          ->whereDate('check_out_date', $hoy)
                          ->orderBy('check_out_date', 'asc')
                ),

            'proximas' => Tab::make('Próximas')
                ->icon('heroicon-o-calendar-days')
                ->badge(static fn () => Reservation::where('status', ReservationStatus::Aprobada)
                    ->whereDate('check_in_date', '>', now()->toDateString())
                    ->count()
                )
                ->badgeColor('info')
                ->deferBadge()
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('status', ReservationStatus::Aprobada)
                          ->whereDate('check_in_date', '>', $hoy)
                          ->orderBy('check_in_date', 'asc')
                ),

            'historial' => Tab::make('Historial')
                ->icon('heroicon-o-archive-box')
                ->badge(static fn () => Reservation::whereIn('status', [
                    ReservationStatus::CheckedOut->value,
                    ReservationStatus::Cancelada->value,
                    ReservationStatus::Rechazada->value,
                ])->count())
                ->badgeColor('gray')
                ->deferBadge()
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->whereIn('status', [
                              ReservationStatus::CheckedOut->value,
                              ReservationStatus::Cancelada->value,
                              ReservationStatus::Rechazada->value,
                          ])
                          ->orderBy('check_out_date', 'desc')
                ),
        ];
    }
}