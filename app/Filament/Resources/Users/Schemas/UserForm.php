<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Nombre completo')
                ->required()
                ->extraInputAttributes(['required' => false])
                ->validationMessages(['required' => 'El nombre completo es obligatorio.'])
                ->maxLength(255)
                ->prefixIcon('heroicon-o-user'),

            TextInput::make('email')
                ->label('Correo electrónico')
                ->email()
                ->required()
                ->extraInputAttributes(['required' => false])
                ->validationMessages([
                    'required' => 'El correo electrónico es obligatorio.',
                    'email'    => 'El correo electrónico no tiene un formato válido.',
                    'unique'   => 'Este correo electrónico ya está en uso.',
                ])
                ->unique(ignoreRecord: true)
                ->maxLength(255)
                ->prefixIcon('heroicon-o-envelope'),

            TextInput::make('phone')
                ->label('Teléfono')
                ->tel()
                ->maxLength(20)
                ->prefixIcon('heroicon-o-phone'),

            Select::make('roles')
                ->label('Rol')
                ->relationship('roles', 'name')
                ->preload()
                ->searchable()
                ->native(false),

            Toggle::make('is_active')
                ->label('Activo')
                ->default(true),

            TextInput::make('password')
                ->label('Contraseña')
                ->password()
                ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                ->dehydrated(fn ($state) => filled($state))
                ->required(fn (string $operation): bool => $operation === 'create')
                ->extraInputAttributes(['required' => false])
                ->validationMessages(['required' => 'La contraseña es obligatoria para nuevos usuarios.'])
                ->maxLength(255)
                ->prefixIcon('heroicon-o-lock-closed'),
        ]);
    }
}