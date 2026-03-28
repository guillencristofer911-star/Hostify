<?php

namespace App\Models;

use App\Enums\ShiftCloseStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class ShiftClose extends Model
{
    use HasUuids, SoftDeletes;

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

    // ─── Acciones de negocio 

    /**
     * Recalcula los totales del turno.
     *
     * total_cash_system  → solo efectivo
     * total_card_system  → datáfono + transferencia 
     */
    public function calculateTotals(): static
    {
        $this->update([
            'total_cash_system' => $this->payments()->cash()->sum('amount'),
            'total_card_system' => $this->payments()->nonCash()->sum('amount'), // ← CORREGIDO
        ]);

        return $this;
    }

    /**
     * Cierra el turno: calcula totales, registra cierre y responsable.
     *
     * @param  float       $cashCounted    Efectivo físico contado por el cajero
     * @param  float       $marginThreshold Margen de tolerancia de diferencia
     * @param  string|null $observations   Notas del cierre
     */
    public function close(
        float $cashCounted,
        float $marginThreshold = 0,
        ?string $observations = null
    ): void {
        if ($this->status !== ShiftCloseStatus::Abierto) {
            throw new \DomainException('Solo se puede cerrar un turno abierto.');
        }

        $this->calculateTotals();
        $this->refresh();

        $difference   = $cashCounted - (float) $this->total_cash_system;
        $withinMargin = abs($difference) <= $marginThreshold;

        $this->update([
            'status'             => ShiftCloseStatus::Cerrado,
            'closed_by'          => Auth::id(),
            'shift_end'          => now(),
            'total_cash_counted' => $cashCounted,
            'difference'         => $difference,
            'within_margin'      => $withinMargin,
            'margin_threshold'   => $marginThreshold,
            'observations'       => $observations,
        ]);
    }

    public function validate(?string $digitalSignature = null): void
    {
        if ($this->status !== ShiftCloseStatus::Cerrado) {
            throw new \DomainException('Solo se puede validar un turno cerrado.');
        }

        $this->update([
            'status'            => ShiftCloseStatus::Validado,
            'validated_by'      => Auth::id(),
            'validated_at'      => now(),
            'digital_signature' => $digitalSignature,
        ]);
    }

    //  Helpers 

    public function isOpen(): bool
    {
        return $this->status === ShiftCloseStatus::Abierto;
    }

    public function isClosed(): bool
    {
        return $this->status === ShiftCloseStatus::Cerrado;
    }

    public function isValidated(): bool
    {
        return $this->status === ShiftCloseStatus::Validado;
    }

    //  Helpers estáticos 


    public static function openForUser(string $userId): ?string
    {
        return self::where('status', ShiftCloseStatus::Abierto)
            ->where('opened_by', $userId)
            ->value('id');
    }

    public static function hasOpenShift(string $userId): bool
    {
        return self::where('status', ShiftCloseStatus::Abierto)
            ->where('opened_by', $userId)
            ->exists();
    }
}