<?php

namespace App\Models;

use App\Enums\RoomStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'room_type_id',
        'number',
        'floor',
        'status',
        'status_changed_at',
        'alert_minutes_threshold',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active'               => 'boolean',
        'floor'                   => 'integer',
        'alert_minutes_threshold' => 'integer',
        'status_changed_at'       => 'datetime',
        'status'                  => RoomStatus::class,
    ];

    // ─── Scopes ──────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_active', true)
                     ->where('status', RoomStatus::Libre);
    }

    // ─── Métodos ─────────────────────────────────────────────────

    public function updateStatus(RoomStatus $newStatus, ?int $changedBy = null, string $source = 'system'): void
    {
        $oldStatus = $this->status?->value ?? 'libre';

        $this->update([
            'status'            => $newStatus->value,
            'status_changed_at' => now(),
        ]);

        RoomStatusLog::create([
            'room_id'     => $this->id,
            'changed_by'  => $changedBy,
            'from_status' => $oldStatus,
            'to_status'   => $newStatus->value,
            'source'      => $source,
            'changed_at'  => now(),
        ]);
    }

    // ─── Relaciones ───────────────────────────────────────────────

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function cleaningSessions(): HasMany
    {
        return $this->hasMany(CleaningSession::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(RoomStatusLog::class);
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(RoomInventory::class);
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class);
    }
}