<?php

namespace App\Filament\Resources\ShiftCloses\Pages;

use App\Filament\Resources\ShiftCloses\ShiftCloseResource;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewShiftClose extends ViewRecord
{
    protected static string $resource = ShiftCloseResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Turno')
                ->columns(3)
                ->schema([
                    TextEntry::make('created_at')
                        ->label('Registro creado')
                        ->dateTime('d/m/Y H:i'),

                    TextEntry::make('shift_start')
                        ->label('Inicio')
                        ->dateTime('d/m/Y H:i'),

                    TextEntry::make('shift_end')
                        ->label('Fin')
                        ->dateTime('d/m/Y H:i')
                        ->placeholder('En curso'),

                    TextEntry::make('openedBy.name')
                        ->label('Abierto por')
                        ->badge()
                        ->color('gray'),

                    TextEntry::make('closedBy.name')
                        ->label('Cerrado por')
                        ->placeholder('—'),

                    TextEntry::make('validatedBy.name')
                        ->label('Validado por')
                        ->placeholder('—'),

                    TextEntry::make('status')
                        ->label('Estado')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'abierto'  => 'warning',
                            'cerrado'  => 'info',
                            'validado' => 'success',
                            default    => 'gray',
                        })
                        ->formatStateUsing(fn (string $state): string => match ($state) {
                            'abierto'  => 'Abierto',
                            'cerrado'  => 'Cerrado',
                            'validado' => 'Validado',
                            default    => $state,
                        }),
                ]),

            Section::make('Resumen de caja')
                ->columns(2)
                ->schema([
                    TextEntry::make('total_cash_system')
                        ->label('Efectivo sistema')
                        ->money('COP'),

                    TextEntry::make('total_card_system')
                        ->label('Datafono sistema')
                        ->money('COP'),

                    TextEntry::make('total_cash_counted')
                        ->label('Efectivo contado')
                        ->money('COP')
                        ->placeholder('—'),

                    TextEntry::make('difference')
                        ->label('Diferencia')
                        ->money('COP')
                        ->placeholder('—')
                        ->color(fn ($record) => match (true) {
                            $record?->difference === null    => 'gray',
                            $record?->within_margin === true => 'success',
                            default                          => 'danger',
                        }),

                    IconEntry::make('within_margin')
                        ->label('Dentro del margen')
                        ->boolean()
                        ->placeholder('—'),

                    TextEntry::make('margin_threshold')
                        ->label('Margen tolerancia')
                        ->money('COP'),
                ]),

            Section::make('Observaciones')
                ->schema([
                    TextEntry::make('observations')
                        ->label('')
                        ->placeholder('Sin observaciones')
                        ->columnSpanFull(),
                ]),

        ]);
    }
}