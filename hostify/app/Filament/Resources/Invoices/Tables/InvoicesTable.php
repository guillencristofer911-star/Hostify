<?php

namespace App\Filament\Resources\Invoices\Tables;

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
                    ->icon('heroicon-o-document-text'),

                TextColumn::make('reservation.guest.full_name')
                    ->label('Huésped')
                    ->searchable()
                    ->icon('heroicon-o-user'),

                TextColumn::make('reservation.room.number')
                    ->label('Habitación')
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-home'),

                TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->money('COP')
                    ->sortable(),

                TextColumn::make('taxes')
                    ->label('Impuestos')
                    ->money('COP'),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('COP')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pagada'    => 'success',
                        'pendiente' => 'warning',
                        'anulada'   => 'danger',
                        default     => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'pagada'    => 'heroicon-o-check-circle',
                        'pendiente' => 'heroicon-o-clock',
                        'anulada'   => 'heroicon-o-x-circle',
                        default     => 'heroicon-o-question-mark-circle',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pagada'    => 'Pagada',
                        'pendiente' => 'Pendiente',
                        'anulada'   => 'Anulada',
                        default     => $state,
                    }),

                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->icon('heroicon-o-calendar'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pagada'    => 'Pagada',
                        'pendiente' => 'Pendiente',
                        'anulada'   => 'Anulada',
                    ]),
            ])
            ->recordActions([
                Action::make('ver')
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn ($record) => route('filament.admin.resources.invoices.view', $record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Eliminar seleccionadas'),
                ]),
            ]);
    }
}