<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'reservation_id',
        'registered_by',
        'shift_close_id',
        'amount',
        'method',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'amount'  => 'decimal:2',
        'paid_at' => 'datetime',
        'method'  => PaymentMethod::class,
    ];

    //  Relaciones 

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function shiftClose(): BelongsTo
    {
        return $this->belongsTo(ShiftClose::class);
    }

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    //  Scopes 

    public function scopeCash($query)
    {
        return $query->where('method', PaymentMethod::Efectivo);
    }

    public function scopeCard($query)
    {
        return $query->where('method', PaymentMethod::Datafono);
    }

    public function scopeTransfer($query)
    {
        return $query->where('method', PaymentMethod::Transferencia);
    }
}