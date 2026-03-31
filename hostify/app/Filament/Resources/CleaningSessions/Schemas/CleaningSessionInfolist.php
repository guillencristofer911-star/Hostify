<?php

namespace App\Filament\Resources\CleaningSessions\Schemas;

use App\Enums\CleaningStatus;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CleaningSessionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Asignación')
                ->icon('heroicon-o-user-circle')
                ->columns(2)
                ->schema([
                    TextEntry::make('room.number')
                        ->label('Habitación')
                        ->badge()
                        ->color('info'),

                    TextEntry::make('assignedTo.name')
                        ->label('Camarera'),

                    TextEntry::make('assignedBy.name')
                        ->label('Asignado por'),

                    TextEntry::make('assigned_date')
                        ->label('Fecha asignada')
                        ->date('d/m/Y'),

                    TextEntry::make('status')
                        ->label('Estado')
                        ->badge()
                        ->formatStateUsing(fn ($state) => $state instanceof CleaningStatus
                            ? $state->label()
                            : $state
                        )
                        ->color(fn ($state) => $state instanceof CleaningStatus
                            ? $state->color()
                            : 'gray'
                        ),
                ]),

            Section::make('Registro de tiempo')
                ->icon('heroicon-o-clock')
                ->columns(3)
                ->schema([
                    TextEntry::make('started_at')
                        ->label('Hora inicio')
                        ->dateTime('H:i')
                        ->placeholder('—'),

                    TextEntry::make('finished_at')
                        ->label('Hora fin')
                        ->dateTime('H:i')
                        ->placeholder('—'),

                    TextEntry::make('duration_minutes')
                        ->label('Duración')
                        ->suffix(' min')
                        ->placeholder('—'),
                ]),

            Section::make('Evidencia')
                ->icon('heroicon-o-camera')
                ->columns(1)
                ->schema([
                    ImageEntry::make('photo_after_url')
                        ->label('Foto post-limpieza')
                        ->height(200)
                        ->visible(fn ($record) => filled($record->photo_after_url)),

                    TextEntry::make('notes')
                        ->label('Notas')
                        ->placeholder('Sin observaciones'),
                ]),
        ]);
    }
}