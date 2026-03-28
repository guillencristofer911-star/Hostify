<?php

namespace App\Enums;

enum CleaningStatus: string
{
    case Pendiente  = 'pendiente';
    case EnProceso  = 'en_proceso';
    case Terminada  = 'terminada';

    public function label(): string
    {
        return match($this) {
            self::Pendiente => 'Pendiente',
            self::EnProceso => 'En proceso',
            self::Terminada => 'Terminada',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Pendiente => 'warning',
            self::EnProceso => 'info',
            self::Terminada => 'success',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::Pendiente => 'heroicon-o-clock',
            self::EnProceso => 'heroicon-o-sparkles',
            self::Terminada => 'heroicon-o-check-circle',
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