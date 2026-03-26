<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guest extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'full_name', 'document_type', 'document_number',
        'phone', 'email', 'nationality', 'notes', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    // Historial completo RF-12
    public function activeReservations(): HasMany
    {
        return $this->hasMany(Reservation::class)
                    ->whereIn('status', ['activa', 'checked_out']);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Nombre + documento para selects
    public function getFullLabelAttribute(): string
    {
        return "{$this->full_name} — {$this->document_number}";
    }
}
