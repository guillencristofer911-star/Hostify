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
                ->maxLength(120),

            Select::make('document_type')
                ->label('Tipo de documento')
                ->options([
                    'CC'        => 'Cédula de Ciudadanía',
                    'CE'        => 'Cédula de Extranjería',
                    'Pasaporte' => 'Pasaporte',
                    'NIT'       => 'NIT',
                ])
                ->required(),

            TextInput::make('document_number')
                ->label('Número de documento')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(30),

            TextInput::make('phone')
                ->label('Teléfono')
                ->tel()
                ->maxLength(20),

            TextInput::make('email')
                ->label('Email')
                ->email()
                ->maxLength(150),

            TextInput::make('nationality')
                ->label('Nacionalidad')
                ->maxLength(60),

            Toggle::make('is_active')
                ->label('Activo')
                ->default(true),

            Textarea::make('notes')
                ->label('Historial y preferencias')
                ->rows(3),
        ]);
    }
}
