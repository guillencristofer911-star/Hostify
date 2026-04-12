<?php

namespace App\Filament\Resources\Guests\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class GuestForm
{
    private const DOCUMENT_TYPES = [
        'CC'        => 'Cédula de Ciudadanía',
        'CE'        => 'Cédula de Extranjería',
        'Pasaporte' => 'Pasaporte',
        'NIT'       => 'NIT',
    ];

    private const EMAIL_REGEX = '/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/';

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('full_name')
                ->label('Nombre completo')
                ->required()
                ->extraInputAttributes(['required' => false])
                ->validationMessages(['required' => 'El nombre completo es obligatorio.'])
                ->maxLength(120)
                ->live(onBlur: true)
                ->afterStateUpdated(function ($component) {
                    $component->getLivewire()->validateOnly($component->getStatePath());
                })
                ->prefixIcon('heroicon-o-user'),

            Select::make('document_type')
                ->label('Tipo de documento')
                ->options(self::DOCUMENT_TYPES)
                ->required()
                ->native(true)
                ->live()
                ->afterStateUpdated(function ($livewire) {
                    $livewire->validateOnly('data.document_number');
                })
                ->validationMessages(['required' => 'El tipo de documento es obligatorio.']),

            TextInput::make('document_number')
                ->label('Número de documento')
                ->required()
                ->extraInputAttributes(['required' => false])
                ->regex('/^[a-zA-Z0-9\-\.]+$/')
                ->rules(function (callable $get): array {
                    if ($get('document_type') === 'NIT') {
                        return ['digits:9'];
                    }
                    return [];
                })
                ->validationMessages([
                    'required' => 'El número de documento es obligatorio.',
                    'regex'    => 'El número de documento solo permite letras, números, guiones y puntos.',
                    'digits'   => 'El NIT debe tener exactamente 9 dígitos numéricos.',
                    'unique'   => 'El número de documento ya está registrado.',
                ])
                ->unique(ignoreRecord: true)
                ->maxLength(30)
                ->live(onBlur: true)
                ->afterStateUpdated(function ($component) {
                    $component->getLivewire()->validateOnly($component->getStatePath());
                })
                ->prefixIcon('heroicon-o-identification'),

            TextInput::make('phone')
                ->label('Teléfono')
                ->tel()
                ->maxLength(20)
                ->live(onBlur: true)
                ->afterStateUpdated(function ($component) {
                    $component->getLivewire()->validateOnly($component->getStatePath());
                })
                ->prefixIcon('heroicon-o-phone'),

            TextInput::make('email')
                ->label('Correo electrónico')
                ->regex(self::EMAIL_REGEX)
                ->validationMessages([
                    'regex' => 'El correo electrónico no tiene un formato válido.',
                ])
                ->maxLength(150)
                ->live(onBlur: true)
                ->afterStateUpdated(function ($component) {
                    $component->getLivewire()->validateOnly($component->getStatePath());
                })
                ->prefixIcon('heroicon-o-envelope'),

            TextInput::make('nationality')
                ->label('Nacionalidad')
                ->maxLength(60)
                ->live(onBlur: true)
                ->afterStateUpdated(function ($component) {
                    $component->getLivewire()->resetValidation($component->getStatePath());
                })
                ->prefixIcon('heroicon-o-flag'),

            Toggle::make('is_active')
                ->label('Activo')
                ->default(true),

            Textarea::make('notes')
                ->label('Historial y preferencias')
                ->rows(3),
        ]);
    }
}