<?php

namespace App\Filament\Resources\Rooms\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use App\Models\RoomType;

class RoomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('room_type_id')
                ->label('Tipo de habitación')
                ->options(RoomType::active()->pluck('name', 'id'))
                ->searchable()
                ->required()
                ->native(false),

            TextInput::make('number')
                ->label('Número de habitación')
                ->required()
                ->maxLength(10)
                ->unique(ignoreRecord: true)
                ->placeholder('101, A2, SUITE-1...')
                ->prefixIcon('heroicon-o-home'),

            TextInput::make('floor')
                ->label('Piso')
                ->numeric()
                ->minValue(1)
                ->prefixIcon('heroicon-o-building-office'),

            Select::make('status')
                ->label('Estado')
                ->options([
                    'libre'         => 'Libre',
                    'sucia'         => 'Sucia',
                    'ocupada'       => 'Ocupada',
                    'no_disponible' => 'No disponible',
                ])
                ->default('libre')
                ->required()
                ->native(false),

            Toggle::make('is_active')
                ->label('Activa')
                ->default(true),

            Textarea::make('notes')
                ->label('Notas internas')
                ->rows(2),
        ]);
    }
}