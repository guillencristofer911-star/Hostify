<?php

namespace App\Models;

use App\Enums\CleaningStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CleaningSession extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'room_id',
        'assigned_to',
        'assigned_by',
        'reservation_id',
        'status',
        'assigned_date',
        'started_at',
        'finished_at',
        'duration_minutes',
        'photo_after_url',
        'notes',
    ];

    protected $casts = [
        'status'        => CleaningStatus::class,
        'assigned_date' => 'date',
        'started_at'    => 'datetime',
        'finished_at'   => 'datetime',
    ];

    //  Scopes 

    public function scopeForHousekeeper(Builder $query, string $userId): Builder
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeActiveToday(Builder $query): Builder
    {
        return $query
            ->whereDate('assigned_date', today())
            ->whereIn('status', [
                CleaningStatus::Pendiente->value,
                CleaningStatus::EnProceso->value,
            ]);
    }

    public function scopePendiente(Builder $query): Builder
    {
        return $query->where('status', CleaningStatus::Pendiente->value);
    }

    public function scopeEnProceso(Builder $query): Builder
    {
        return $query->where('status', CleaningStatus::EnProceso->value);
    }

    //  Relaciones 

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }
}