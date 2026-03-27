<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Reservation extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'guest_id', 'room_id', 'created_by', 'source', 'status',
        'check_in_date', 'check_out_date', 'actual_check_in',
        'actual_check_out', 'rate', 'pre_register_token',
        'rejection_reason',
    ];

    protected $casts = [
        'check_in_date'    => 'date',
        'check_out_date'   => 'date',
        'actual_check_in'  => 'datetime',
        'actual_check_out' => 'datetime',
        'rate'             => 'decimal:2',
    ];

    // Relaciones

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function charges(): HasMany
    {
        return $this->hasMany(Charge::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function cleaningSessions(): HasMany
    {
        return $this->hasMany(CleaningSession::class);
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class);
    }

    // Helpers

    // Mínimo 1 noche para evitar totales en cero
    public function getNightsAttribute(): int
    {
        return max(1, (int) $this->check_in_date->diffInDays($this->check_out_date));
    }

    public function getRoomTotalAttribute(): float
    {
        return $this->nights * (float) $this->rate;
    }

    public function getTotalChargesAttribute(): float
    {
        return (float) $this->charges->sum('amount');
    }

    public function getInvoiceTotalAttribute(): float
    {
        return $this->room_total + $this->total_charges;
    }

    public function generateToken(): void
    {
        $this->update(['pre_register_token' => Str::random(64)]);
    }

    // Scopes

    public function scopePending($query)
    {
        return $query->where('status', 'pendiente');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'activa');
    }
}
