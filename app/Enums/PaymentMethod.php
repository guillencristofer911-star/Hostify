<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Efectivo      = 'efectivo';
    case Datafono      = 'datafono';
    case Transferencia = 'transferencia';

    public function label(): string
    {
        return match($this) {
            self::Efectivo      => 'Efectivo',
            self::Datafono      => 'Datáfono',
            self::Transferencia => 'Transferencia',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::Efectivo      => 'heroicon-o-banknotes',
            self::Datafono      => 'heroicon-o-credit-card',
            self::Transferencia => 'heroicon-o-arrow-path',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Efectivo      => 'success',
            self::Datafono      => 'info',
            self::Transferencia => 'warning',
        };
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