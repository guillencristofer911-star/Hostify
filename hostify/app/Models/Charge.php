<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Charge extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'reservation_id',
        'registered_by',
        'description',
        'amount',
        'charged_at',
    ];

    protected $casts = [
        'amount'     => 'decimal:2',
        'charged_at' => 'datetime',
    ];

    //  Relaciones 

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }
}