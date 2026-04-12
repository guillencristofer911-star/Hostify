<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

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
        'status'       => \App\Enums\InvoiceStatus::class,
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


    public static function generateNumber(): string
    {

        $acquired = DB::selectOne('SELECT pg_try_advisory_xact_lock(?) AS locked', [987654321]);

        if (! $acquired?->locked) {
            DB::selectOne('SELECT pg_advisory_xact_lock(?)', [987654321]);
        }

        $year = now()->year;
        $prefix = "F-{$year}-";

        $last = DB::table('invoices')
            ->whereNull('deleted_at')
            ->where('invoice_number', 'like', $prefix . '%')
            ->max('invoice_number');

        $sequence = $last
            ? (int) substr($last, strrpos($last, '-') + 1)
            : 0;

        return sprintf('F-%d-%04d', $year, $sequence + 1);
    }
}