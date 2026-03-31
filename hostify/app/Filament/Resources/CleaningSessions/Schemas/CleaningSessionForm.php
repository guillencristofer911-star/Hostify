<?php

namespace App\Filament\Resources\CleaningSessions\Schemas;

use App\Enums\CleaningStatus;
use App\Models\Room;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class CleaningSessionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Asignación')
                ->icon('heroicon-o-user-circle')
                ->columns(2)
                ->schema([
                    Select::make('room_id')
                        ->label('Habitación')
                        ->options(
                            Room::where('is_active', true)
                                ->orderBy('number')
                                ->pluck('number', 'id')
                        )
                        ->searchable()
                        ->required()
                        ->columnSpan(1),

                    Select::make('assigned_to')
                        ->label('Camarera asignada')
                        ->options(
                            User::where('role', 'housekeeper')
                                ->where('is_active', true)
                                ->orderBy('name')
                                ->pluck('name', 'id')
                        )
                        ->searchable()
                        ->required()
                        ->default(fn () => Auth::id())
                        ->disabled(function (): bool {
                            /** @var \App\Models\User|null $user */
                            $user = Auth::user();
                            return $user instanceof \App\Models\User && $user->hasRole('housekeeper');
                        })
                        ->dehydrated(true)
                        ->columnSpan(1),

                    DatePicker::make('assigned_date')
                        ->label('Fecha asignada')
                        ->default(today())
                        ->required()
                        ->columnSpan(1),

                    Select::make('status')
                        ->label('Estado')
                        ->options(CleaningStatus::options())
                        ->default(CleaningStatus::Pendiente->value)
                        ->required()
                        ->columnSpan(1),
                ]),

            Section::make('Registro de tiempo')
                ->icon('heroicon-o-clock')
                ->columns(2)
                ->collapsed()
                ->schema([
                    TimePicker::make('started_at')
                        ->label('Hora inicio')
                        ->seconds(false)
                        ->columnSpan(1),

                    TimePicker::make('finished_at')
                        ->label('Hora fin')
                        ->seconds(false)
                        ->columnSpan(1),

                    TextInput::make('duration_minutes')
                        ->label('Duración (minutos)')
                        ->numeric()
                        ->disabled()
                        ->helperText('Se calcula automáticamente al terminar')
                        ->columnSpan(1),
                ]),

            Section::make('Evidencia')
                ->icon('heroicon-o-camera')
                ->columns(1)
                ->collapsed()
                ->schema([
                    FileUpload::make('photo_after_url')
                        ->label('Foto post-limpieza')
                        ->image()
                        ->directory('cleaning-photos')
                        ->maxSize(5120)
                        ->helperText('Máximo 5MB. JPG o PNG.'),

                    Textarea::make('notes')
                        ->label('Notas / Observaciones')
                        ->placeholder('Ej: Falta toalla en baño, TV sin control...')
                        ->rows(3)
                        ->maxLength(500),
                ]),
        ]);
    }
}