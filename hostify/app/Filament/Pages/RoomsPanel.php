<?php

namespace App\Filament\Pages;

use App\Models\Room;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use UnitEnum;

class RoomsPanel extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::Home;
    protected static ?string $navigationLabel               = 'Panel Habitaciones';
    protected static string|UnitEnum|null $navigationGroup  = 'Operaciones';
    protected static ?string $title                         = 'Panel de Habitaciones';
    protected static ?int    $navigationSort                = 3;

    protected string $view = 'filament.pages.rooms-panel';

    protected ?string $pollingInterval = '30s';

    public array $openMenus = [];

    public function toggleMenu(string $roomId): void
    {
        if (isset($this->openMenus[$roomId])) {
            unset($this->openMenus[$roomId]);
        } else {
            $this->openMenus = [$roomId => true];
        }
    }

    public function changeStatus(string $roomId, string $status): void
    {
        $allowed = ['libre', 'ocupada', 'sucia', 'no_disponible'];

        if (!in_array($status, $allowed)) {
            return;
        }

        $room = Room::findOrFail($roomId);
        $room->updateStatus($status);

        unset($this->openMenus[$roomId]);

        $labels = [
            'libre'         => 'Libre 🟢',
            'ocupada'       => 'Ocupada 🔴',
            'sucia'         => 'Sucia 🟡',
            'no_disponible' => 'No disponible ⚫',
        ];

        Notification::make()
            ->title("Hab. {$room->number} → {$labels[$status]}")
            ->success()
            ->send();
    }

    public function getRooms(): Collection
    {
        return Room::active()
            ->with(['roomType', 'reservations' => function ($q) {
                $q->where('status', 'activa')->with('guest');
            }])
            ->orderBy('floor')
            ->orderBy('number')
            ->get()
            ->groupBy('floor');
    }

    public function getViewData(): array
    {
        return [
            'pisos'   => $this->getRooms(),
            'resumen' => [
                'libre'         => Room::active()->where('status', 'libre')->count(),
                'ocupada'       => Room::active()->where('status', 'ocupada')->count(),
                'sucia'         => Room::active()->where('status', 'sucia')->count(),
                'no_disponible' => Room::active()->where('status', 'no_disponible')->count(),
                'total'         => Room::active()->count(),
            ],
        ];
    }
}
