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
                ->maxLength(80)
                ->placeholder('Doble, Suite, Triple...')
                ->prefixIcon('heroicon-o-tag'),

            TextInput::make('base_price')
                ->label('Precio base por noche')
                ->required()
                ->numeric()
                ->prefix('$')
                ->minValue(0)
                ->prefixIcon('heroicon-o-banknotes'),

            TextInput::make('capacity')
                ->label('Capacidad máx. huéspedes')
                ->required()
                ->numeric()
                ->minValue(1)
                ->maxValue(20)
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