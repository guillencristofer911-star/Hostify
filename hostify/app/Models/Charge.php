<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Charge extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'reservation_id',
        'registered_by',
        'type',
        'description',
        'quantity',
        'unit_price',
        'amount',
        'charged_at',
    ];

    protected $casts = [
        'unit_price'  => 'decimal:2',
        'amount'      => 'decimal:2',
        'quantity'    => 'integer',
        'charged_at'  => 'datetime',
    ];

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }
}