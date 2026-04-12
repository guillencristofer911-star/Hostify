<?php

namespace App\Filament\Resources\Reservations\Schemas;

use App\Enums\ReservationStatus;
use App\Enums\RoomStatus;
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
                ->placeholder('Selecciona un huésped')
                ->searchable()
                ->searchPrompt('Busca por nombre o documento...')
                ->noSearchResultsMessage('No se encontraron huéspedes.')
                ->getSearchResultsUsing(function (string $search) {
                    return Guest::active()
                        ->where(function ($q) use ($search) {
                            $q->where('full_name', 'ilike', "%{$search}%")
                              ->orWhere('document_number', 'ilike', "%{$search}%");
                        })
                        ->limit(20)
                        ->get()
                        ->mapWithKeys(fn ($g) => [$g->id => $g->full_label]);
                })
                ->getOptionLabelUsing(function ($value) {
                    $g = Guest::find($value);
                    return $g?->full_label ?? $value;
                })
                ->required()
                ->live()
                ->afterStateUpdated(function ($component) {
                    $component->getLivewire()->resetValidation($component->getStatePath());
                })
                ->validationMessages(['required' => 'El campo Huésped es obligatorio.'])
                ->createOptionForm([
                    TextInput::make('full_name')
                        ->label('Nombre completo')
                        ->required()
                        ->extraInputAttributes(['required' => false])
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($component) {
                            $component->getLivewire()->resetValidation($component->getStatePath());
                        })
                        ->validationMessages(['required' => 'El nombre completo es obligatorio.']),
                    Select::make('document_type')
                        ->label('Tipo documento')
                        ->options([
                            'CC'        => 'Cédula',
                            'CE'        => 'Cédula Extranjería',
                            'Pasaporte' => 'Pasaporte',
                        ])
                        ->required()
                        ->native(false)
                        ->live()
                        ->afterStateUpdated(function ($component) {
                            $component->getLivewire()->resetValidation($component->getStatePath());
                        })
                        ->validationMessages(['required' => 'El tipo de documento es obligatorio.']),
                    TextInput::make('document_number')
                        ->label('Número documento')
                        ->required()
                        ->extraInputAttributes(['required' => false])
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($component) {
                            $component->getLivewire()->resetValidation($component->getStatePath());
                        })
                        ->validationMessages(['required' => 'El número de documento es obligatorio.']),
                    TextInput::make('phone')
                        ->label('Teléfono')
                        ->tel()
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($component) {
                            $component->getLivewire()->resetValidation($component->getStatePath());
                        }),
                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($component) {
                            $component->getLivewire()->resetValidation($component->getStatePath());
                        })
                        ->validationMessages(['email' => 'El correo electrónico no tiene un formato válido.']),
                ])
                ->createOptionUsing(function (array $data) {
                    return Guest::create(array_merge($data, ['is_active' => true]))->id;
                }),

            // FECHAS
            DatePicker::make('check_in_date')
                ->label('Fecha entrada')
                ->required()
                ->native(false)
                ->validationMessages([
                    'required'       => 'La fecha de entrada es obligatoria.',
                    'date'           => 'La fecha de entrada no tiene un formato válido.',
                    'after_or_equal' => 'La fecha de entrada no puede ser anterior a hoy.',
                ])
                ->minDate(today())
                ->live()
                ->afterStateUpdated(function ($component, Set $set) {
                    $component->getLivewire()->resetValidation($component->getStatePath());
                    $set('check_out_date', null);
                    $set('room_id', null);
                }),

            DatePicker::make('check_out_date')
                ->label('Fecha salida')
                ->required()
                ->native(false)
                ->validationMessages([
                    'required' => 'La fecha de salida es obligatoria.',
                    'date'     => 'La fecha de salida no tiene un formato válido.',
                    'after'    => 'La fecha de salida debe ser posterior a la fecha de entrada.',
                ])
                ->minDate(fn (Get $get) => $get('check_in_date')
                    ? \Carbon\Carbon::parse($get('check_in_date'))->addDay()
                    : today()->addDay()
                )
                ->live()
                ->afterStateUpdated(function ($component, Set $set) {
                    $component->getLivewire()->resetValidation($component->getStatePath());
                    $set('room_id', null);
                }),

            // HABITACIÓN
            Select::make('room_id')
                ->label('Habitación')
                ->placeholder('Selecciona una habitación')
                ->required()
                ->searchable()
                ->searchPrompt('Busca por número, piso o tipo...')
                ->noSearchResultsMessage('No hay habitaciones disponibles para esas fechas.')
                ->validationMessages(['required' => 'El campo Habitación es obligatorio.'])
                ->getSearchResultsUsing(function (string $search, Get $get) {
                    $checkIn  = $get('check_in_date');
                    $checkOut = $get('check_out_date');

                    $query = Room::active()
                        ->with('roomType')
                        ->where(function ($q) use ($search) {
                            $q->where('number', 'ilike', "%{$search}%")
                              ->orWhereHas('roomType', fn ($q2) =>
                                  $q2->where('name', 'ilike', "%{$search}%")
                              )
                              ->orWhere('floor', 'ilike', "%{$search}%");
                        });

                    if ($checkIn && $checkOut) {
                        $ocupadas = Reservation::whereIn('status', [
                                ReservationStatus::Aprobada,
                                ReservationStatus::Activa,
                            ])
                            ->where('check_in_date', '<', $checkOut)
                            ->where('check_out_date', '>', $checkIn)
                            ->pluck('room_id')
                            ->toArray();

                        $query->whereNotIn('id', $ocupadas);
                    } else {
                        $query->where('status', RoomStatus::Libre);
                    }

                    return $query
                        ->orderBy('floor')
                        ->orderBy('number')
                        ->limit(30)
                        ->get()
                        ->mapWithKeys(function ($r) {
                            $label = 'Piso ' . $r->floor
                                . ' · Hab. ' . $r->number
                                . ' — ' . $r->roomType->name
                                . ' ($' . number_format($r->roomType->base_price, 0, ',', '.') . ')';
                            return [$r->id => $label];
                        });
                })
                ->getOptionLabelUsing(function ($value) {
                    $room = Room::with('roomType')->find($value);
                    if (! $room) return $value;
                    return 'Piso ' . $room->floor
                        . ' · Hab. ' . $room->number
                        . ' — ' . $room->roomType->name
                        . ' ($' . number_format($room->roomType->base_price, 0, ',', '.') . ')';
                })
                ->live()
                ->afterStateUpdated(function ($component, $state, Set $set) {
                    $component->getLivewire()->resetValidation($component->getStatePath());
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
                ->extraInputAttributes(['required' => false])
                ->validationMessages([
                    'required' => 'La tarifa por noche es obligatoria.',
                    'numeric'  => 'La tarifa por noche debe ser un número válido.',
                ])
                ->live(onBlur: true)
                ->afterStateUpdated(function ($component) {
                    $component->getLivewire()->resetValidation($component->getStatePath());
                })
                ->helperText('Se llena automáticamente al seleccionar habitación. Puedes modificarla.'),

        ]);
    }
}