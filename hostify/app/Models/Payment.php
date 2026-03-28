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

    /** Pagos en efectivo */
    public function scopeCash($query)
    {
        return $query->where('method', PaymentMethod::Efectivo);
    }

    /**
     * Pagos no-efectivo: datáfono + transferencia.
     * Se agrupan en total_card_system porque la tabla shift_closes
     * no tiene columna separada para transferencias.
     */
    public function scopeCard($query)
    {
        return $query->whereIn('method', [
            PaymentMethod::Datafono,
            PaymentMethod::Transferencia,
        ]);
    }

    /** Alias semántico de scopeCard() */
    public function scopeNonCash($query)
    {
        return $query->whereIn('method', [
            PaymentMethod::Datafono,
            PaymentMethod::Transferencia,
        ]);
    }

    public function scopeDatasphone($query)
    {
        return $query->where('method', PaymentMethod::Datafono);
    }

    public function scopeTransfer($query)
    {
        return $query->where('method', PaymentMethod::Transferencia);
    }
}