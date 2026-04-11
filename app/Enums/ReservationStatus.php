<?php

namespace App\Enums;

enum ReservationStatus: string
{
    case Pendiente  = 'pendiente';
    case Aprobada   = 'aprobada';
    case Activa     = 'activa';
    case CheckedOut = 'checked_out';
    case Rechazada  = 'rechazada';
    case Cancelada  = 'cancelada';

    public function label(): string
    {
        return match($this) {
            self::Pendiente  => 'Pendiente',
            self::Aprobada   => 'Aprobada',
            self::Activa     => 'Activa',
            self::CheckedOut => 'Check-out',
            self::Rechazada  => 'Rechazada',
            self::Cancelada  => 'Cancelada',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Pendiente  => 'warning',
            self::Aprobada   => 'info',
            self::Activa     => 'success',
            self::CheckedOut => 'gray',
            self::Rechazada  => 'danger',
            self::Cancelada  => 'danger',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::Pendiente  => 'heroicon-o-clock',
            self::Aprobada   => 'heroicon-o-check-circle',
            self::Activa     => 'heroicon-o-home',
            self::CheckedOut => 'heroicon-o-arrow-left-on-rectangle',
            self::Rechazada  => 'heroicon-o-x-circle',
            self::Cancelada  => 'heroicon-o-no-symbol',
        };
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::CheckedOut, self::Rechazada, self::Cancelada]);
    }

    public function canEdit(): bool
    {
        return ! $this->isFinal();
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return array_column(
            array_map(fn ($case) => ['value' => $case->value, 'label' => $case->label()], self::cases()),
            'label',
            'value'
        );
    }
}