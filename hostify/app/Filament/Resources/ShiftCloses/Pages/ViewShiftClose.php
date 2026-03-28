<?php

namespace App\Filament\Resources\ShiftCloses\Pages;

use App\Enums\ShiftCloseStatus;
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
                ->icon('heroicon-o-clock')
                ->columns(3)
                ->schema([
                    TextEntry::make('created_at')
                        ->label('Registro creado')
                        ->dateTime('d/m/Y H:i')
                        ->icon('heroicon-o-calendar'),

                    TextEntry::make('shift_start')
                        ->label('Inicio')
                        ->dateTime('d/m/Y H:i')
                        ->icon('heroicon-o-play'),

                    TextEntry::make('shift_end')
                        ->label('Fin')
                        ->dateTime('d/m/Y H:i')
                        ->placeholder('En curso')
                        ->icon('heroicon-o-stop'),

                    TextEntry::make('openedBy.name')
                        ->label('Abierto por')
                        ->badge()
                        ->color('gray')
                        ->icon('heroicon-o-user'),

                    TextEntry::make('closedBy.name')
                        ->label('Cerrado por')
                        ->placeholder('—')
                        ->icon('heroicon-o-user'),

                    TextEntry::make('validatedBy.name')
                        ->label('Validado por')
                        ->placeholder('—')
                        ->icon('heroicon-o-user'),

                    TextEntry::make('status')
                        ->label('Estado')
                        ->badge()
                        ->color(fn (ShiftCloseStatus $state): string => $state->color())
                        ->icon(fn (ShiftCloseStatus $state): string => $state->icon())
                        ->formatStateUsing(fn (ShiftCloseStatus $state): string => $state->label()),
                ]),

            Section::make('Resumen de caja')
                ->icon('heroicon-o-banknotes')
                ->columns(2)
                ->schema([
                    TextEntry::make('total_cash_system')
                        ->label('Efectivo sistema')
                        ->money('COP')
                        ->icon('heroicon-o-banknotes'),

                    TextEntry::make('total_card_system')
                        ->label('Datáfono sistema')
                        ->money('COP')
                        ->icon('heroicon-o-credit-card'),

                    TextEntry::make('total_cash_counted')
                        ->label('Efectivo contado')
                        ->money('COP')
                        ->placeholder('—')
                        ->icon('heroicon-o-calculator'),

                    TextEntry::make('difference')
                        ->label('Diferencia')
                        ->money('COP')
                        ->placeholder('—')
                        ->icon('heroicon-o-arrows-right-left')
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
                        ->money('COP')
                        ->icon('heroicon-o-adjustments-horizontal'),
                ]),

            Section::make('Observaciones')
                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                ->schema([
                    TextEntry::make('observations')
                        ->label('')
                        ->placeholder('Sin observaciones')
                        ->columnSpanFull(),
                ]),

        ]);
    }
}