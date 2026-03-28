<?php

namespace App\Filament\Resources\ShiftCloses\Tables;

use App\Models\ShiftClose;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ShiftClosesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('openedBy.name')
                    ->label('Recepcionista')
                    ->searchable()
                    ->icon('heroicon-o-user')
                    ->toggleable(),

                TextColumn::make('shift_start')
                    ->label('Inicio turno')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->icon('heroicon-o-play')
                    ->toggleable(),

                TextColumn::make('shift_end')
                    ->label('Fin turno')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('En curso')
                    ->icon('heroicon-o-stop')
                    ->toggleable(),

                TextColumn::make('total_cash_system')
                    ->label('Efectivo sistema')
                    ->money('COP')
                    ->icon('heroicon-o-banknotes')
                    ->toggleable(),

                TextColumn::make('total_card_system')
                    ->label('Datáfono sistema')
                    ->money('COP')
                    ->icon('heroicon-o-credit-card')
                    ->toggleable(),

                TextColumn::make('total_cash_counted')
                    ->label('Efectivo contado')
                    ->money('COP')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('difference')
                    ->label('Diferencia')
                    ->money('COP')
                    ->placeholder('—')
                    ->color(fn ($record) => match (true) {
                        $record?->difference === null    => 'gray',
                        $record?->within_margin === true => 'success',
                        default                          => 'danger',
                    })
                    ->toggleable(),

                IconColumn::make('within_margin')
                    ->label('En margen')
                    ->boolean()
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'abierto'  => 'warning',
                        'cerrado'  => 'info',
                        'validado' => 'success',
                        default    => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'abierto'  => 'heroicon-o-lock-open',
                        'cerrado'  => 'heroicon-o-lock-closed',
                        'validado' => 'heroicon-o-check-badge',
                        default    => 'heroicon-o-question-mark-circle',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'abierto'  => 'Abierto',
                        'cerrado'  => 'Cerrado',
                        'validado' => 'Validado',
                        default    => $state,
                    })
                    ->toggleable(),

                TextColumn::make('validatedBy.name')
                    ->label('Validado por')
                    ->placeholder('—')
                    ->icon('heroicon-o-check-badge')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('validated_at')
                    ->label('Validado el')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('observations')
                    ->label('Observaciones')
                    ->limit(40)
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('shift_start', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'abierto'  => 'Abierto',
                        'cerrado'  => 'Cerrado',
                        'validado' => 'Validado',
                    ]),
            ])
            ->recordActions([
                // CERRAR TURNO
                Action::make('cerrar')
                    ->label('Cerrar turno')
                    ->icon('heroicon-o-lock-closed')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Cerrar turno')
                    ->modalIcon('heroicon-o-lock-closed')
                    ->form([
                        TextInput::make('total_cash_counted')
                            ->label('Efectivo contado en caja ($)')
                            ->numeric()
                            ->prefix('$')
                            ->required(),
                    ])
                    ->visible(fn (ShiftClose $record): bool => $record->status === 'abierto')
                    ->action(function (ShiftClose $record, array $data) {
                        $record->calculateTotals();
                        $record->refresh();

                        $counted    = (float) $data['total_cash_counted'];
                        $system     = (float) $record->total_cash_system;
                        $difference = $counted - $system;
                        $margin     = (float) ($record->margin_threshold ?? 5000);

                        $record->update([
                            'closed_by'          => Auth::id(),
                            'shift_end'          => now(),
                            'total_cash_counted' => $counted,
                            'difference'         => $difference,
                            'within_margin'      => abs($difference) <= $margin,
                            'status'             => 'cerrado',
                        ]);

                        Notification::make()
                            ->title('Turno cerrado')
                            ->body(
                                abs($difference) <= $margin
                                    ? '✅ Diferencia dentro del margen: $' . number_format(abs($difference), 0, ',', '.')
                                    : '⚠️ Diferencia fuera del margen: $' . number_format(abs($difference), 0, ',', '.')
                            )
                            ->color(abs($difference) <= $margin ? 'success' : 'warning')
                            ->send();
                    }),

                // VALIDAR TURNO
                Action::make('validar')
                    ->label('Validar')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Validar cierre de turno')
                    ->modalIcon('heroicon-o-check-badge')
                    ->modalDescription('Confirmas que los valores están correctos y el turno queda validado.')
                    ->visible(fn (ShiftClose $record): bool => $record->status === 'cerrado')
                    ->action(function (ShiftClose $record) {
                        $record->update([
                            'validated_by' => Auth::id(),
                            'validated_at' => now(),
                            'status'       => 'validado',
                        ]);

                        Notification::make()
                            ->title('Turno validado')
                            ->icon('heroicon-o-check-badge')
                            ->success()
                            ->send();
                    }),

                Action::make('ver')
                    ->label('Ver detalle')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn (ShiftClose $record) => static::getUrl('view', ['record' => $record])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Eliminar seleccionados'),
                ]),
            ]);
    }

    private static function getUrl(string $name, array $parameters = []): string
    {
        return \App\Filament\Resources\ShiftCloses\ShiftCloseResource::getUrl($name, $parameters);
    }
}