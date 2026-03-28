<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CamareraAssignment extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id', 'room_id', 'assigned_date',
        'assigned_by', 'is_active',
    ];

    protected $casts = [
        'assigned_date' => 'date',
        'is_active'     => 'boolean',
    ];

    //  Relaciones 

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    //  Scopes 

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeToday($query)
    {
        return $query->where('assigned_date', today());
    }
}