<?php

namespace App\Filament\Resources\Guests\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class GuestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('full_name')
                ->label('Nombre completo')
                ->required()
                ->extraInputAttributes(['required' => false])
                ->validationMessages(['required' => 'El nombre completo es obligatorio.'])
                ->maxLength(120)
                ->prefixIcon('heroicon-o-user'),

            Select::make('document_type')
                ->label('Tipo de documento')
                ->options([
                    'CC'        => 'Cédula de Ciudadanía',
                    'CE'        => 'Cédula de Extranjería',
                    'Pasaporte' => 'Pasaporte',
                    'NIT'       => 'NIT',
                ])
                ->required()
                ->native(false)
                ->validationMessages(['required' => 'El tipo de documento es obligatorio.']),

            TextInput::make('document_number')
                ->label('Número de documento')
                ->required()
                ->extraInputAttributes(['required' => false])
                ->validationMessages(['required' => 'El número de documento es obligatorio.'])
                ->unique(ignoreRecord: true)
                ->maxLength(30)
                ->prefixIcon('heroicon-o-identification'),

            TextInput::make('phone')
                ->label('Teléfono')
                ->tel()
                ->maxLength(20)
                ->prefixIcon('heroicon-o-phone'),

            TextInput::make('email')
                ->label('Correo electrónico')
                ->email()
                ->maxLength(150)
                ->validationMessages(['email' => 'El correo electrónico no tiene un formato válido.'])
                ->prefixIcon('heroicon-o-envelope'),

            TextInput::make('nationality')
                ->label('Nacionalidad')
                ->maxLength(60)
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