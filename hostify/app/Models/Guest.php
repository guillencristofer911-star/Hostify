<?php

namespace App\Models;

use App\Enums\ReservationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guest extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'full_name', 'document_type', 'document_number',
        'phone', 'email', 'nationality', 'notes', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    //  Relaciones 

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

 
    public function stayHistory(): HasMany
    {
        return $this->hasMany(Reservation::class)
                    ->whereIn('status', [
                        ReservationStatus::Activa->value,
                        ReservationStatus::CheckedOut->value,
                    ]);
    }

    //  Scopes 

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    //  Accessors 

    public function getFullLabelAttribute(): string
    {
        return "{$this->full_name} — {$this->document_number}";
    }
}