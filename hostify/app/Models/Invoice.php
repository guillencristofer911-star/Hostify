<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'reservation_id',
        'guest_id',
        'invoice_number',
        'subtotal',
        'extras_total',
        'taxes',
        'total',
        'status',
        'issued_at',
        'sent_at',
        'sent_to_email',
    ];

    protected $casts = [
        'subtotal'     => 'decimal:2',
        'extras_total' => 'decimal:2',
        'taxes'        => 'decimal:2',
        'total'        => 'decimal:2',
        'issued_at'    => 'datetime',
        'sent_at'      => 'datetime',
    ];

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function charges(): HasMany
    {
        return $this->hasMany(Charge::class, 'reservation_id', 'reservation_id');
    }
}