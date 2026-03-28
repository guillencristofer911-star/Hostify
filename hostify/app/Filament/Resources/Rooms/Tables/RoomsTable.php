<?php

namespace App\Filament\Resources\Rooms\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class RoomsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->label('Habitación')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-home'),

                TextColumn::make('roomType.name')
                    ->label('Tipo')
                    ->badge()
                    ->sortable()
                    ->icon('heroicon-o-tag'),

                TextColumn::make('floor')
                    ->label('Piso')
                    ->sortable()
                    ->icon('heroicon-o-building-office'),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'libre'         => 'success',
                        'sucia'         => 'warning',
                        'ocupada'       => 'danger',
                        'no_disponible' => 'gray',
                        default         => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'libre'         => 'heroicon-o-check-circle',
                        'sucia'         => 'heroicon-o-sparkles',
                        'ocupada'       => 'heroicon-o-lock-closed',
                        'no_disponible' => 'heroicon-o-no-symbol',
                        default         => 'heroicon-o-question-mark-circle',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'libre'         => 'Libre',
                        'sucia'         => 'Sucia',
                        'ocupada'       => 'Ocupada',
                        'no_disponible' => 'No disponible',
                        default         => $state,
                    }),

                TextColumn::make('roomType.base_price')
                    ->label('Precio/noche')
                    ->money('COP')
                    ->icon('heroicon-o-banknotes'),

                IconColumn::make('is_active')
                    ->label('Activa')
                    ->boolean(),
            ])
            ->defaultSort('number')
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'libre'         => 'Libre',
                        'sucia'         => 'Sucia',
                        'ocupada'       => 'Ocupada',
                        'no_disponible' => 'No disponible',
                    ]),

                SelectFilter::make('room_type_id')
                    ->label('Tipo')
                    ->relationship('roomType', 'name'),

                SelectFilter::make('floor')
                    ->label('Piso')
                    ->options([
                        1 => 'Piso 1',
                        2 => 'Piso 2',
                        3 => 'Piso 3',
                        4 => 'Piso 4',
                    ]),

                TernaryFilter::make('is_active')
                    ->label('Activa'),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Editar')
                    ->icon('heroicon-o-pencil-square'),

                DeleteAction::make()
                    ->label('Eliminar')
                    ->icon('heroicon-o-trash'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Eliminar seleccionados'),
                ]),
            ]);
    }
}