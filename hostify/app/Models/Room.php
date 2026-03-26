<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'room_type_id', 'number', 'floor',
        'status', 'is_active', 'status_changed_at', 'notes'
    ];

    protected $casts = [
        'is_active'         => 'boolean',
        'status_changed_at' => 'datetime',
        'floor'             => 'integer',
    ];

    // Relaciones 
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

    public function camareraAssignments(): HasMany
    {
        return $this->hasMany(CamareraAssignment::class);
    }

    public function roomInventory(): HasMany
    {
        return $this->hasMany(RoomInventory::class);
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_active', true)->where('status', 'libre');
    }

    //  Helpers
    public function updateStatus(string $status): void
    {
        $this->update([
            'status'            => $status,
            'status_changed_at' => now(),
        ]);
    }

    public function isAvailable(): bool
    {
        return $this->status === 'libre' && $this->is_active;
    }
}
