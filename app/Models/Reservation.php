<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use App\Enums\ReservationSource;
use App\Enums\ReservationStatus;
use App\Enums\RoomStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Reservation extends Model
{
    use HasUuids, SoftDeletes, HasFactory;

    protected $fillable = [
        'guest_id',
        'room_id',
        'created_by',
        'source',
        'status',
        'check_in_date',
        'check_out_date',
        'actual_check_in',
        'actual_check_out',
        'rate',
        'pre_register_token',
        'rejection_reason',
    ];

    protected $casts = [
        'check_in_date'    => 'date',
        'check_out_date'   => 'date',
        'actual_check_in'  => 'datetime',
        'actual_check_out' => 'datetime',
        'rate'             => 'decimal:2',
        'status'           => ReservationStatus::class,
        'source'           => ReservationSource::class,
    ];

    //  Relaciones

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function charges(): HasMany
    {
        return $this->hasMany(Charge::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function cleaningSessions(): HasMany
    {
        return $this->hasMany(CleaningSession::class);
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class);
    }

    //  Accessors

    public function getNightsAttribute(): int
    {
        return max(1, (int) $this->check_in_date->diffInDays($this->check_out_date));
    }

    public function getRoomTotalAttribute(): float
    {
        return $this->nights * (float) $this->rate;
    }

    public function getTotalChargesAttribute(): float
    {
        return (float) $this->charges->sum('amount');
    }

    public function getInvoiceTotalAttribute(): float
    {
        return $this->room_total + $this->total_charges;
    }

    /**
     * FIX BUG-005: Título descriptivo para Filament v4 (View, breadcrumb, título de página).
     * Formato: "Reserva #abc12345 · Carlos López · 20 abr → 25 abr"
     * Usa los primeros 8 caracteres del UUID como referencia corta legible.
     */
    public function getTitleAttribute(): string
    {
        $ref      = strtoupper(substr($this->id, 0, 8));
        $guest    = $this->guest?->full_name ?? 'Sin huésped';
        $checkIn  = $this->check_in_date?->translatedFormat('d M') ?? '—';
        $checkOut = $this->check_out_date?->translatedFormat('d M') ?? '—';

        return "Reserva #{$ref} · {$guest} · {$checkIn} → {$checkOut}";
    }

    //  Acciones de negocio

    public function approve(): void
    {
        if ($this->status !== ReservationStatus::Pendiente) {
            throw new \DomainException('Solo se pueden aprobar reservas pendientes.');
        }

        $this->update(['status' => ReservationStatus::Aprobada]);
    }

    public function reject(string $reason): void
    {
        if ($this->status !== ReservationStatus::Pendiente) {
            throw new \DomainException('Solo se pueden rechazar reservas pendientes.');
        }

        $this->update([
            'status'           => ReservationStatus::Rechazada,
            'rejection_reason' => $reason,
        ]);
    }

    public function cancel(): void
    {
        if (! in_array($this->status, [ReservationStatus::Pendiente, ReservationStatus::Aprobada])) {
            throw new \DomainException('Solo se pueden cancelar reservas pendientes o aprobadas.');
        }

        $this->update(['status' => ReservationStatus::Cancelada]);
    }

    public function checkin(): void
    {
        $this->loadMissing(['room', 'guest']);

        if ($this->status !== ReservationStatus::Aprobada) {
            throw new \DomainException('Solo se puede registrar entrada en reservas aprobadas.');
        }

        if (! $this->room) {
            throw new \DomainException('La reserva no tiene habitación asignada.');
        }

        $this->update([
            'status'          => ReservationStatus::Activa,
            'actual_check_in' => now(),
        ]);

        $this->room->updateStatus(RoomStatus::Ocupada);
    }

    /**
     * Registra la salida, genera factura y registra el pago.
     *
     * @param  float       $amount  Monto recibido
     * @param  string      $method  Valor de PaymentMethod (efectivo|datafono|transferencia)
     * @param  string|null $notes   Observaciones opcionales
     *
     * @throws \DomainException Si la reserva no cumple las condiciones para checkout
     */
    public function checkout(float $amount, string $method, ?string $notes = null): void
    {
        $this->loadMissing(['room', 'charges', 'invoice']);

        //  Guards

        if ($this->status !== ReservationStatus::Activa) {
            throw new \DomainException('Solo se puede registrar salida en reservas activas.');
        }

        if (! $this->room) {
            throw new \DomainException('La reserva no tiene habitación asignada.');
        }

        if (! $this->guest_id) {
            throw new \DomainException('La reserva no tiene huésped asignado.');
        }

        if ($this->invoice) {
            throw new \DomainException('La reserva ya tiene una factura generada.');
        }

        $shiftCloseId = ShiftClose::openForUser(Auth::id());

        if (! $shiftCloseId) {
            throw new \DomainException('No hay un turno abierto. Abre un turno antes de registrar la salida.');
        }

        if ($amount <= 0) {
            throw new \DomainException('El monto del pago debe ser mayor a cero.');
        }

        DB::transaction(function () use ($amount, $method, $notes, $shiftCloseId) {

            $subtotal = $this->invoice_total;

            // 1. Actualizar estado de reserva
            $this->update([
                'status'           => ReservationStatus::CheckedOut,
                'actual_check_out' => now(),
            ]);

            $this->room->updateStatus(RoomStatus::Sucia);

            $this->invoice()->create([
                'guest_id'       => $this->guest_id,
                'invoice_number' => Invoice::generateNumber(),
                'subtotal'       => $subtotal,
                'taxes'          => 0,
                'total'          => $subtotal,
                'status'         => InvoiceStatus::Pagada,
                'issued_at'      => now(),
            ]);

            // 4. Registrar pago
            $this->payments()->create([
                'registered_by'  => Auth::id(),
                'shift_close_id' => $shiftCloseId,
                'amount'         => $amount,
                'method'         => $method,
                'paid_at'        => now(),
                'notes'          => $notes,
            ]);
        });
    }

    public function generateToken(): void
    {
        $this->update(['pre_register_token' => Str::random(64)]);
    }

    //  Scopes

    public function scopePending($query)
    {
        return $query->where('status', ReservationStatus::Pendiente);
    }

    public function scopeActive($query)
    {
        return $query->where('status', ReservationStatus::Activa);
    }
}