<?php

namespace App\Filament\Resources\ShiftCloses\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class ShiftCloseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            TextInput::make('opened_by_display')
                ->label('Abierto por')
                ->default(fn () => Auth::user()?->name ?? '—')
                ->disabled()
                ->dehydrated(false)
                ->helperText('Se asigna automáticamente al usuario autenticado'),

            DateTimePicker::make('shift_start')
                ->label('Inicio de turno')
                ->required()
                ->native(false)
                ->validationMessages([
                    'required' => 'La hora de inicio de turno es obligatoria.',
                    'date'     => 'La fecha y hora de inicio no tienen un formato válido.',
                ])
                ->default(now())
                ->displayFormat('d/m/Y H:i')
                ->seconds(false),

            TextInput::make('margin_threshold')
                ->label('Margen de tolerancia ($)')
                ->numeric()
                ->prefix('$')
                ->default(5000)
                ->helperText('Diferencia máxima permitida en caja'),

            Textarea::make('observations')
                ->label('Observaciones')
                ->rows(3)
                ->placeholder('Notas del turno (opcional)'),

        ]);
    }
}