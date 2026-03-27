<?php

namespace App\Filament\Resources\Reservations\Schemas;

use App\Models\Guest;
use App\Models\Room;
use App\Models\Reservation;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ReservationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            // HUÉSPED
            Select::make('guest_id')
                ->label('Huésped')
                ->options(
                    Guest::active()
                        ->get()
                        ->mapWithKeys(fn ($g) => [
                            $g->id => $g->full_name . ' — ' . $g->document_number
                        ])
                )
                ->searchable()
                ->required()
                ->createOptionForm([
                    TextInput::make('full_name')
                        ->label('Nombre completo')
                        ->required(),
                    Select::make('document_type')
                        ->label('Tipo documento')
                        ->options([
                            'CC'        => 'Cédula',
                            'CE'        => 'Cédula Extranjería',
                            'Pasaporte' => 'Pasaporte',
                        ])
                        ->required(),
                    TextInput::make('document_number')
                        ->label('Número documento')
                        ->required(),
                    TextInput::make('phone')
                        ->label('Teléfono')
                        ->tel(),
                    TextInput::make('email')
                        ->label('Email')
                        ->email(),
                ])
                ->createOptionUsing(function (array $data) {
                    return Guest::create(array_merge($data, ['is_active' => true]))->id;
                }),

            // FECHAS
            DatePicker::make('check_in_date')
                ->label('Fecha entrada')
                ->required()
                ->minDate(today())
                ->live()
                ->afterStateUpdated(function (Set $set) {
                    $set('check_out_date', null);
                    $set('room_id', null);
                }),

            DatePicker::make('check_out_date')
                ->label('Fecha salida')
                ->required()
                ->minDate(fn (Get $get) => $get('check_in_date') ?? today())
                ->live()
                ->afterStateUpdated(function (Set $set) {
                    $set('room_id', null);
                }),

            // HABITACIÓN
            Select::make('room_id')
                ->label('Habitación')
                ->required()
                ->options(function (Get $get) {
                    $checkIn  = $get('check_in_date');
                    $checkOut = $get('check_out_date');

                    $query = Room::active()->with('roomType');

                    if ($checkIn && $checkOut) {
                        $ocupadas = Reservation::whereIn('status', ['aprobada', 'activa'])
                            ->where('check_in_date', '<', $checkOut)
                            ->where('check_out_date', '>', $checkIn)
                            ->pluck('room_id')
                            ->toArray();

                        $query->whereNotIn('id', $ocupadas);
                    } else {
                        $query->where('status', 'libre');
                    }

                    return $query->get()->mapWithKeys(function ($r) {
                        $label = 'Hab. ' . $r->number
                            . ' — ' . $r->roomType->name
                            . ' ($' . number_format($r->roomType->base_price, 0, ',', '.') . ')';
                        return [$r->id => $label];
                    });
                })
                ->searchable()
                ->live()
                ->afterStateUpdated(function ($state, Set $set) {
                    if ($state) {
                        $room = Room::with('roomType')->find($state);
                        $set('rate', $room?->roomType?->base_price);
                    }
                }),

            // TARIFA
            TextInput::make('rate')
                ->label('Tarifa por noche')
                ->numeric()
                ->prefix('$')
                ->required()
                ->helperText('Se llena automáticamente al seleccionar habitación. Puedes modificarla.'),

        ]);
    }
}
