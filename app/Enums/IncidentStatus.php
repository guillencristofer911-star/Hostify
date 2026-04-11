<?php

namespace App\Enums;

enum IncidentStatus: string
{
    case Pendiente  = 'pendiente';
    case EnProceso  = 'en_proceso';
    case Resuelto   = 'resuelto';

    public function label(): string
    {
        return match($this) {
            self::Pendiente => 'Pendiente',
            self::EnProceso => 'En proceso',
            self::Resuelto  => 'Resuelto',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Pendiente => 'danger',
            self::EnProceso => 'warning',
            self::Resuelto  => 'success',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::Pendiente => 'heroicon-o-exclamation-circle',
            self::EnProceso => 'heroicon-o-wrench-screwdriver',
            self::Resuelto  => 'heroicon-o-check-circle',
        };
    }

    public function isClosed(): bool
    {
        return $this === self::Resuelto;
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