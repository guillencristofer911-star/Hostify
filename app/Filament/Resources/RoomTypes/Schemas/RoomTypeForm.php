<?php

namespace App\Filament\Resources\RoomTypes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class RoomTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Nombre del tipo')
                ->required()
                ->extraInputAttributes(['required' => false])
                ->validationMessages(['required' => 'El nombre del tipo de habitación es obligatorio.'])
                ->maxLength(80)
                ->placeholder('Doble, Suite, Triple...')
                ->live(onBlur: true)
                ->afterStateUpdated(function ($component) {
                    $component->getLivewire()->resetValidation($component->getStatePath());
                })
                ->prefixIcon('heroicon-o-tag'),

            TextInput::make('base_price')
                ->label('Precio base por noche')
                ->required()
                ->extraInputAttributes(['required' => false])
                ->validationMessages([
                    'required' => 'El precio base por noche es obligatorio.',
                    'numeric'  => 'El precio base debe ser un número válido.',
                    'min'      => 'El precio base debe ser mayor o igual a cero.',
                ])
                ->numeric()
                ->prefix('$')
                ->minValue(0)
                ->live(onBlur: true)
                ->afterStateUpdated(function ($component) {
                    $component->getLivewire()->resetValidation($component->getStatePath());
                })
                ->prefixIcon('heroicon-o-banknotes'),

            TextInput::make('capacity')
                ->label('Capacidad máx. huéspedes')
                ->required()
                ->extraInputAttributes(['required' => false])
                ->validationMessages([
                    'required' => 'La capacidad máxima de huéspedes es obligatoria.',
                    'numeric'  => 'La capacidad debe ser un número entero.',
                    'min'      => 'La capacidad mínima es 1 huésped.',
                    'max'      => 'La capacidad máxima permitida es 20 huéspedes.',
                ])
                ->numeric()
                ->minValue(1)
                ->maxValue(20)
                ->live(onBlur: true)
                ->afterStateUpdated(function ($component) {
                    $component->getLivewire()->resetValidation($component->getStatePath());
                })
                ->prefixIcon('heroicon-o-users'),

            Textarea::make('description')
                ->label('Descripción')
                ->rows(3),

            Toggle::make('is_active')
                ->label('Activo')
                ->default(true),
        ]);
    }
}