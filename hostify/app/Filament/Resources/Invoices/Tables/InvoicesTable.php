<?php

namespace App\Filament\Resources\Invoices\Tables;

use App\Enums\InvoiceStatus;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('# Factura')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-document-text')
                    ->toggleable(),

                TextColumn::make('reservation.guest.full_name')
                    ->label('Huésped')
                    ->searchable()
                    ->icon('heroicon-o-user')
                    ->toggleable(),

                TextColumn::make('reservation.room.number')
                    ->label('Habitación')
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-home')
                    ->toggleable(),

                TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->money('COP')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('taxes')
                    ->label('Impuestos')
                    ->money('COP')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('COP')
                    ->sortable()
                    ->weight('bold')
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (InvoiceStatus $state): string => $state->color())
                    ->icon(fn (InvoiceStatus $state): string => $state->icon())
                    ->formatStateUsing(fn (InvoiceStatus $state): string => $state->label())
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->icon('heroicon-o-calendar')
                    ->toggleable(),

                TextColumn::make('sent_at')
                    ->label('Enviada el')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('No enviada')
                    ->icon('heroicon-o-envelope')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('sent_to_email')
                    ->label('Email destino')
                    ->placeholder('—')
                    ->icon('heroicon-o-at-symbol')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options(InvoiceStatus::options()),
            ])
            ->recordActions([
                Action::make('ver')
                    ->label('Ver factura')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn ($record) => route('filament.admin.resources.invoices.view', $record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Eliminar seleccionadas')
                        ->modalHeading('Eliminar facturas seleccionadas')
                        ->modalDescription('Esta acción no se puede deshacer.')
                        ->modalSubmitActionLabel('Sí, eliminar')
                        ->modalCancelActionLabel('Cancelar'),
                ]),
            ]);
    }
}