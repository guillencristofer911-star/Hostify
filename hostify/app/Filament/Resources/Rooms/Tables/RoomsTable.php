<?php

namespace App\Filament\Resources\Rooms\Tables;

use App\Enums\RoomStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
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
                    ->color(fn (RoomStatus $state): string => $state->color())
                    ->icon(fn (RoomStatus $state): string => $state->icon())
                    ->formatStateUsing(fn (RoomStatus $state): string => $state->label()),

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
                    ->options(RoomStatus::options()),

                SelectFilter::make('room_type_id')
                    ->label('Tipo')
                    ->relationship('roomType', 'name'),

                SelectFilter::make('floor')
                    ->label('Piso')
                    ->relationship('floor', 'floor'),

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