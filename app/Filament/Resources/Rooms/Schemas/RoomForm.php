<?php

namespace App\Filament\Resources\Rooms\Schemas;

use App\Enums\RoomStatus;
use App\Models\RoomType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

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
                ->native(false)
                ->validationMessages(['required' => 'El tipo de habitación es obligatorio.']),

            TextInput::make('number')
                ->label('Número de habitación')
                ->required()
                ->extraInputAttributes(['required' => false])
                ->validationMessages(['required' => 'El número de habitación es obligatorio.'])
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
                ->options(RoomStatus::options())
                ->default(RoomStatus::Libre->value)
                ->required()
                ->native(false)
                ->validationMessages(['required' => 'El estado de la habitación es obligatorio.']),

            Toggle::make('is_active')
                ->label('Activa')
                ->default(true),

            Textarea::make('notes')
                ->label('Notas internas')
                ->rows(2),
        ]);
    }
}