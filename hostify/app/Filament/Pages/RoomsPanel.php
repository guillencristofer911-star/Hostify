<?php

namespace App\Filament\Pages;

use App\Enums\RoomStatus;
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
        if (! RoomStatus::tryFrom($status)) {
            return;
        }

        $room = Room::findOrFail($roomId);
        $room->updateStatus(RoomStatus::from($status));

        unset($this->openMenus[$roomId]);

        Notification::make()
            ->title("Hab. {$room->number} — " . RoomStatus::from($status)->label())
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
                    $room->status = RoomStatus::Ocupada;
                } elseif ($room->status === RoomStatus::Ocupada) {
                    $room->status = RoomStatus::Libre;
                }
            })
            ->groupBy('floor');
    }

    public function getViewData(): array
    {
        $date        = $this->parsedDate();
        $occupiedIds = $this->occupiedIds($date);
        $allRooms    = Room::active()->with('roomType')->get();

        $counts = array_fill_keys(
            array_column(RoomStatus::cases(), 'value'),
            0
        );

        foreach ($allRooms as $room) {
            if (in_array($room->id, $occupiedIds)) {
                $counts[RoomStatus::Ocupada->value]++;
            } else {
                $counts[$room->status->value]++;
            }
        }

        return [
            'pisos'   => $this->getRooms(),
            'resumen' => [
                'libre'         => $counts[RoomStatus::Libre->value],
                'ocupada'       => $counts[RoomStatus::Ocupada->value],
                'sucia'         => $counts[RoomStatus::Sucia->value],
                'no_disponible' => $counts[RoomStatus::NoDisponible->value],
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