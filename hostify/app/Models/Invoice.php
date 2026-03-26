<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasUuids;

    protected $fillable = [
        'reservation_id', 'invoice_number', 'subtotal',
        'taxes', 'total', 'status', 'sent_at', 'sent_to_email'
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

    // Genera número consecutivo automático RF-26
    public static function generateNumber(): string
    {
        $last = self::max('invoice_number');
        $next = $last ? (intval(substr($last, 2)) + 1) : 1;
        return 'F-' . str_pad($next, 5, '0', STR_PAD_LEFT);
    }
}
