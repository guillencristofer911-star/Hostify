<?php

namespace App\Filament\Pages;

use App\Enums\CleaningStatus;
use App\Enums\ReservationStatus;
use App\Enums\RoomStatus;
use App\Models\CleaningSession;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;
use UnitEnum;

class RoomsPanel extends Page
{
    use WithFileUploads;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Home;
    protected static ?string $navigationLabel               = 'Panel Habitaciones';
    protected static string|UnitEnum|null $navigationGroup  = 'Operaciones';
    protected static ?string $title                         = 'Panel de Habitaciones';
    protected static ?int    $navigationSort                = 3;

    protected string $view = 'filament.pages.rooms-panel';

    protected ?string $pollingInterval = '30s';

    //  Estado de UI 
    public array  $openMenus    = [];
    public string $filterDate   = '';
    public bool   $showNoteModal = false;

    // ─── Modal de nota/foto
    public string  $noteRoomId        = '';
    public string  $noteSessionId     = '';
    public string  $noteRoomNumber    = '';
    public string  $noteText          = '';
    public         $notePhoto         = null;

    public function mount(): void
    {
        $this->filterDate = now()->toDateString();
    }

    //  Visibilidad según rol 

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) return false;

        return $user->hasAnyRole(['owner', 'supervisor', 'receptionist', 'housekeeper']);
    }

    //  Acciones de estado 

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
        if (! RoomStatus::tryFrom($status)) return;

        $room = Room::findOrFail($roomId);
        $room->updateStatus(RoomStatus::from($status), Auth::id(), 'manual');

        unset($this->openMenus[$roomId]);

        Notification::make()
            ->title("Hab. {$room->number} → " . RoomStatus::from($status)->label())
            ->success()
            ->send();
    }

    //  Acciones de limpieza (camarera) RF-18 

    public function startCleaning(string $sessionId): void
    {
        $session = CleaningSession::findOrFail($sessionId);

        if ((string) $session->assigned_to !== (string) Auth::id()) {
            Notification::make()->title('Sin permiso')->danger()->send();
            return;
        }

        $session->update([
            'status'     => CleaningStatus::EnProceso->value,
            'started_at' => now(),
        ]);

        $session->room->updateStatus(RoomStatus::Sucia, Auth::id(), 'system');

        Notification::make()
            ->title("Limpieza iniciada — Hab. {$session->room->number}")
            ->success()
            ->send();
    }

    public function finishCleaning(string $sessionId): void
    {
        $session = CleaningSession::findOrFail($sessionId);

        if ($session->assigned_to !== Auth::id()) {
            Notification::make()->title('Sin permiso')->danger()->send();
            return;
        }

        $started = $session->started_at ?? now();
        $minutes = (int) $started->diffInMinutes(now());

        $session->update([
            'status'           => CleaningStatus::Terminada->value,
            'finished_at'      => now(),
            'duration_minutes' => $minutes,
        ]);

        $session->room->updateStatus(RoomStatus::Libre, Auth::id(), 'system');

        Notification::make()
            ->title("✓ Hab. {$session->room->number} lista — {$minutes} min")
            ->success()
            ->send();
    }

    //  Modal nota/foto RF-19 

    public function openNoteModal(string $sessionId, string $roomId, string $roomNumber): void
    {
        $this->noteSessionId  = $sessionId;
        $this->noteRoomId     = $roomId;
        $this->noteRoomNumber = $roomNumber;
        $this->noteText       = '';
        $this->notePhoto      = null;
        $this->showNoteModal  = true;
    }

    public function closeNoteModal(): void
    {
        $this->showNoteModal = false;
        $this->noteText      = '';
        $this->notePhoto     = null;
    }

    public function saveNote(): void
    {
        $this->validate([
            'noteText'  => 'required|string|max:500',
            'notePhoto' => 'nullable|image|max:4096',
        ], [
            'noteText.required' => 'La nota no puede estar vacía.',
            'noteText.max'      => 'Máximo 500 caracteres.',
            'notePhoto.image'   => 'Solo se permiten imágenes.',
            'notePhoto.max'     => 'Máximo 4MB.',
        ]);

        $session = CleaningSession::findOrFail($this->noteSessionId);

        $photoPath = null;
        if ($this->notePhoto) {
            $photoPath = $this->notePhoto->store('cleaning-photos', 'public');
        }

        $session->update([
            'notes'           => $this->noteText,
            'photo_after_url' => $photoPath ?? $session->photo_after_url,
        ]);

        $this->closeNoteModal();

        Notification::make()
            ->title("Nota guardada — Hab. {$this->noteRoomNumber}")
            ->success()
            ->send();
    }

    //  Datos de la vista 

    public function getRooms(): Collection
    {
        /** @var User|null $user */
        $user        = Auth::user();
        $date        = $this->parsedDate();
        $occupiedIds = $this->occupiedIds($date);

        $query = Room::active()
            ->with(['roomType', 'reservations' => function ($q) {
                $q->whereIn('status', [
                      ReservationStatus::Activa->value,
                      ReservationStatus::Aprobada->value,
                  ])
                  ->whereDate('check_in_date', '<=', $this->parsedDate())
                  ->whereDate('check_out_date', '>', $this->parsedDate())
                  ->with('guest');
            }])
            ->orderBy('floor')
            ->orderBy('number');

        // RF-17: camarera solo ve sus habitaciones asignadas hoy
        if ($user && $user->hasRole('housekeeper')) {
            $assignedRoomIds = CleaningSession::forHousekeeper($user->id)
                ->activeToday()
                ->pluck('room_id')
                ->toArray();

            $query->whereIn('id', $assignedRoomIds);
        }

        return $query->get()
            ->each(function ($room) use ($occupiedIds) {
                if (in_array($room->id, $occupiedIds)) {
                    $room->status = RoomStatus::Ocupada;
                } elseif ($room->status === RoomStatus::Ocupada) {
                    $room->status = RoomStatus::Libre;
                }
            })
            ->groupBy('floor');
    }

    public function getSessionForRoom(string $roomId): ?CleaningSession
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) return null;

        return CleaningSession::forHousekeeper($user->id)
            ->activeToday()
            ->where('room_id', $roomId)
            ->first();
    }

    public function getViewData(): array
    {
        $date        = $this->parsedDate();
        $occupiedIds = $this->occupiedIds($date);
        $allRooms    = Room::active()->with('roomType')->get();

        /** @var User|null $user */
        $user = Auth::user();
        $isHousekeeper = $user && $user->hasRole('housekeeper');

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
            'pisos'         => $this->getRooms(),
            'isHousekeeper' => $isHousekeeper,
            'resumen'       => [
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
        return Reservation::whereIn('status', [
                ReservationStatus::Activa->value,
                ReservationStatus::Aprobada->value,
            ])
            ->whereDate('check_in_date', '<=', $date)
            ->whereDate('check_out_date', '>', $date)
            ->pluck('room_id')
            ->filter()
            ->toArray();
    }
}