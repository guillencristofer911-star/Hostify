<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case Borrador = 'borrador';
    case Emitida  = 'emitida';
    case Pagada   = 'pagada';
    case Anulada  = 'anulada';

    public function label(): string
    {
        return match($this) {
            self::Borrador => 'Borrador',
            self::Emitida  => 'Emitida',
            self::Pagada   => 'Pagada',
            self::Anulada  => 'Anulada',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Borrador => 'gray',
            self::Emitida  => 'info',
            self::Pagada   => 'success',
            self::Anulada  => 'danger',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::Borrador => 'heroicon-o-document',
            self::Emitida  => 'heroicon-o-document-check',
            self::Pagada   => 'heroicon-o-check-badge',
            self::Anulada  => 'heroicon-o-x-circle',
        };
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::Pagada, self::Anulada]);
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