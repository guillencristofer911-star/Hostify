<?php

namespace App\Filament\Resources\Reservations\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ReservationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('guest_id')
                    ->relationship('guest', 'id')
                    ->required(),
                Select::make('room_id')
                    ->relationship('room', 'id'),
                TextInput::make('created_by')
                    ->numeric(),
                TextInput::make('source')
                    ->required()
                    ->default('manual_reception'),
                TextInput::make('status')
                    ->required()
                    ->default('pendiente'),
                DatePicker::make('check_in_date')
                    ->required(),
                DatePicker::make('check_out_date')
                    ->required(),
                DateTimePicker::make('actual_check_in'),
                DateTimePicker::make('actual_check_out'),
                TextInput::make('rate')
                    ->required()
                    ->numeric(),
                Textarea::make('rejection_reason')
                    ->columnSpanFull(),
            ]);
    }
}
