<?php

namespace App\Filament\Pages;

use App\Models\Reservation;
use App\Models\Room;
use BackedEnum;
use Carbon\Carbon;
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

    public array  $openMenus  = [];
    public string $filterDate = '';

    public function mount(): void
    {
        $this->filterDate = now()->toDateString();
    }

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

        if (! in_array($status, $allowed)) {
            return;
        }

        $room = Room::findOrFail($roomId);
        $room->updateStatus($status);

        unset($this->openMenus[$roomId]);

        $labels = [
            'libre'         => 'Libre',
            'ocupada'       => 'Ocupada',
            'sucia'         => 'Sucia',
            'no_disponible' => 'No disponible',
        ];

        Notification::make()
            ->title("Hab. {$room->number} — {$labels[$status]}")
            ->success()
            ->send();
    }

    public function getRooms(): Collection
    {
        $date        = $this->parsedDate();
        $occupiedIds = $this->occupiedIds($date);

        return Room::active()
            ->with(['roomType', 'reservations' => function ($q) use ($date) {
                $q->whereIn('status', ['activa', 'aprobada'])
                  ->whereDate('check_in_date', '<=', $date)
                  ->whereDate('check_out_date', '>', $date)
                  ->with('guest');
            }])
            ->orderBy('floor')
            ->orderBy('number')
            ->get()
            ->each(function ($room) use ($occupiedIds) {
                if (in_array($room->id, $occupiedIds)) {
                    $room->status = 'ocupada';
                } elseif ($room->status === 'ocupada') {
                    $room->status = 'libre';
                }
            })
            ->groupBy('floor');
    }

    public function getViewData(): array
    {
        $date        = $this->parsedDate();
        $occupiedIds = $this->occupiedIds($date);
        $allRooms    = Room::active()->with('roomType')->get();

        $libre         = 0;
        $ocupada       = 0;
        $sucia         = 0;
        $no_disponible = 0;

        foreach ($allRooms as $room) {
            if (in_array($room->id, $occupiedIds)) {
                $ocupada++;
            } elseif ($room->status === 'sucia') {
                $sucia++;
            } elseif ($room->status === 'no_disponible') {
                $no_disponible++;
            } else {
                $libre++;
            }
        }

        return [
            'pisos'   => $this->getRooms(),
            'resumen' => [
                'libre'         => $libre,
                'ocupada'       => $ocupada,
                'sucia'         => $sucia,
                'no_disponible' => $no_disponible,
                'total'         => $allRooms->count(),
            ],
        ];
    }

    //  Helpers privados 

    private function parsedDate(): Carbon
    {
        return $this->filterDate
            ? Carbon::parse($this->filterDate)
            : now();
    }

    private function occupiedIds(Carbon $date): array
    {
        return Reservation::whereIn('status', ['activa', 'aprobada'])
            ->whereDate('check_in_date', '<=', $date)
            ->whereDate('check_out_date', '>', $date)
            ->pluck('room_id')
            ->filter()
            ->toArray();
    }
}