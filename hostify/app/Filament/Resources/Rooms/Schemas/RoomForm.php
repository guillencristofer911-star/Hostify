<?php

namespace App\Filament\Resources\Rooms\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class RoomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('room_type_id')
                    ->relationship('roomType', 'name')
                    ->required(),
                TextInput::make('number')
                    ->required(),
                TextInput::make('floor')
                    ->numeric(),
                TextInput::make('status')
                    ->required()
                    ->default('libre'),
                Toggle::make('is_active')
                    ->required(),
                DateTimePicker::make('status_changed_at'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
