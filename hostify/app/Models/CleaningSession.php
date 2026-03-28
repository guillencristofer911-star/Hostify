<?php

namespace App\Models;

use App\Enums\RoomStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CleaningSession extends Model
{
    use HasUuids;

    protected $fillable = [
        'room_id', 'assigned_to', 'reservation_id',
        'status', 'started_at', 'finished_at',
        'duration_minutes', 'photo_after_url', 'notes',
    ];

    protected $casts = [
        'started_at'  => 'datetime',
        'finished_at' => 'datetime',
    ];

    //  Relaciones 

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function inventoryChecks(): HasMany
    {
        return $this->hasMany(InventoryCheck::class);
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class);
    }

    //  Acciones de negocio 


    public function finish(): void
    {
        $this->loadMissing('room');

        $finished = now();

        $this->update([
            'status'           => 'terminada',
            'finished_at'      => $finished,
            'duration_minutes' => $this->started_at->diffInMinutes($finished),
        ]);

        $this->room->updateStatus(RoomStatus::Libre);
    }
}