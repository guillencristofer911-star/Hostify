<?php

namespace App\Enums;

enum RoomStatus: string
{
    case Libre         = 'libre';
    case Ocupada       = 'ocupada';
    case Sucia         = 'sucia';
    case NoDisponible  = 'no_disponible';

    public function label(): string
    {
        return match($this) {
            self::Libre        => 'Libre',
            self::Ocupada      => 'Ocupada',
            self::Sucia        => 'Sucia',
            self::NoDisponible => 'No disponible',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Libre        => 'success',
            self::Ocupada      => 'danger',
            self::Sucia        => 'warning',
            self::NoDisponible => 'gray',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::Libre        => 'heroicon-o-check-circle',
            self::Ocupada      => 'heroicon-o-lock-closed',
            self::Sucia        => 'heroicon-o-sparkles',
            self::NoDisponible => 'heroicon-o-no-symbol',
        };
    }

    public function isAvailable(): bool
    {
        return $this === self::Libre;
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