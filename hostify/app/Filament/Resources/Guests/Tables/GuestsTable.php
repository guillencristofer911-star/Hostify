<?php

namespace App\Filament\Resources\Guests\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class GuestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label('Nombre completo')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-user'),

                TextColumn::make('document_type')
                    ->label('Tipo doc.')
                    ->badge()
                    ->icon('heroicon-o-identification'),

                TextColumn::make('document_number')
                    ->label('Documento')
                    ->searchable()
                    ->icon('heroicon-o-hashtag'),

                TextColumn::make('phone')
                    ->label('Teléfono')
                    ->icon('heroicon-o-phone'),

                TextColumn::make('email')
                    ->label('Correo electrónico')
                    ->searchable()
                    ->icon('heroicon-o-envelope'),

                TextColumn::make('reservations_count')
                    ->label('Reservas')
                    ->counts('reservations')
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-calendar-days'),

                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Estado activo'),
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