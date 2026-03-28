<?php

namespace App\Models;

use App\Enums\ShiftCloseStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShiftClose extends Model
{
    use HasUuids;

    protected $fillable = [
        'opened_by', 'closed_by', 'validated_by',
        'shift_start', 'shift_end',
        'total_cash_system', 'total_card_system',
        'total_cash_counted', 'difference',
        'within_margin', 'margin_threshold',
        'observations', 'validated_at',
        'digital_signature', 'status',
    ];

    protected $casts = [
        'shift_start'        => 'datetime',
        'shift_end'          => 'datetime',
        'validated_at'       => 'datetime',
        'total_cash_system'  => 'decimal:2',
        'total_card_system'  => 'decimal:2',
        'total_cash_counted' => 'decimal:2',
        'difference'         => 'decimal:2',
        'margin_threshold'   => 'decimal:2',
        'within_margin'      => 'boolean',
        'status'             => ShiftCloseStatus::class,
    ];

    //  Relaciones 

    public function openedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    //  Acciones de negocio 


    public function calculateTotals(): static
    {
        $this->update([
            'total_cash_system' => $this->payments()->cash()->sum('amount'),
            'total_card_system' => $this->payments()->card()->sum('amount'),
        ]);

        return $this;
    }

    //  Helpers estáticos 


    public static function openForUser(string $userId): ?string
    {
        return self::where('status', ShiftCloseStatus::Abierto)
            ->where('opened_by', $userId)
            ->value('id');
    }
}