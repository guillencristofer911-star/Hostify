<?php

namespace App\Models;

use App\Enums\ReservationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Reservation extends Model
{
    use HasUuids, SoftDeletes;

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

        $this->room->updateStatus('ocupada');
    }

    public function checkout(float $amount, string $method, ?string $notes = null): void
    {
        $this->loadMissing(['room', 'charges', 'invoice']);

        if ($this->status !== ReservationStatus::Activa) {
            throw new \DomainException('Solo se puede registrar salida en reservas activas.');
        }

        if (! $this->room) {
            throw new \DomainException('La reserva no tiene habitación asignada.');
        }

        if ($this->invoice) {
            throw new \DomainException('La reserva ya tiene una factura generada.');
        }

        $subtotal = $this->invoice_total;

        $this->update([
            'status'           => ReservationStatus::CheckedOut,
            'actual_check_out' => now(),
        ]);

        $this->room->updateStatus('sucia');

        $this->invoice()->create([
            'invoice_number' => Invoice::generateNumber(),
            'subtotal'       => $subtotal,
            'taxes'          => 0,
            'total'          => $subtotal,
            'status'         => 'pagada',
        ]);

        $this->payments()->create([
            'registered_by' => Auth::id(),
            'amount'        => $amount,
            'method'        => $method,
            'paid_at'       => now(),
            'notes'         => $notes,
        ]);
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