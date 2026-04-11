<?php

namespace App\Enums;

enum ShiftCloseStatus: string
{
    case Abierto  = 'abierto';
    case Cerrado  = 'cerrado';
    case Validado = 'validado';

    public function label(): string
    {
        return match($this) {
            self::Abierto  => 'Abierto',
            self::Cerrado  => 'Cerrado',
            self::Validado => 'Validado',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Abierto  => 'success',
            self::Cerrado  => 'warning',
            self::Validado => 'info',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::Abierto  => 'heroicon-o-lock-open',
            self::Cerrado  => 'heroicon-o-lock-closed',
            self::Validado => 'heroicon-o-check-badge',
        };
    }

    public function isOpen(): bool
    {
        return $this === self::Abierto;
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