<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use HasUuids, SoftDeletes;

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
        'status'   => InvoiceStatus::class,
    ];

    //  Relaciones 

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    //  Helpers 

    public static function generateNumber(): string
    {
        return 'F-' . now()->format('YmdHisv') . '-' . Str::upper(Str::random(4));
    }
}