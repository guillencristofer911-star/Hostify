<?php

namespace App\Models;

use App\Enums\IncidentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Incident extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'room_id',
        'reservation_id',
        'cleaning_session_id',
        'reported_by',
        'category',
        'status',
        'description',
        'photo_url',
        'resolved_at',
        'resolved_by',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'status'      => IncidentStatus::class,
    ];

    //  Relaciones 

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function cleaningSession(): BelongsTo
    {
        return $this->belongsTo(CleaningSession::class);
    }

    public function reportedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    //  Scopes 

    public function scopePending($query)
    {
        return $query->where('status', IncidentStatus::Pendiente);
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', [IncidentStatus::Pendiente, IncidentStatus::EnProceso]);
    }
}