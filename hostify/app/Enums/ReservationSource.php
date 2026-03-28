<?php

namespace App\Enums;

enum ReservationSource: string
{
    case WebForm          = 'web_form';
    case ManualReception  = 'manual_reception';

    public function label(): string
    {
        return match ($this) {
            self::WebForm         => 'Web',
            self::ManualReception => 'Recepción',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::WebForm         => 'heroicon-o-globe-alt',
            self::ManualReception => 'heroicon-o-computer-desktop',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::WebForm         => 'info',
            self::ManualReception => 'gray',
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