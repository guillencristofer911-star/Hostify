<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use HasUuids;

    protected $fillable = [
        'reservation_id',
        'invoice_number',
        'subtotal',
        'taxes',
        'total',
        'status',
        'sent_at',
        'sent_to_email',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'taxes'    => 'decimal:2',
        'total'    => 'decimal:2',
        'sent_at'  => 'datetime',
    ];

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public static function generateNumber(): string
    {
        return 'F-' . now()->format('YmdHisv') . '-' . Str::upper(Str::random(4));
    }
}