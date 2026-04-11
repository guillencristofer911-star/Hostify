<?php

namespace App\Filament\Resources\ShiftCloses\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ShiftCloseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('opened_by')
                    ->numeric(),
                TextEntry::make('closed_by')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('validated_by')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('shift_start')
                    ->dateTime(),
                TextEntry::make('shift_end')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('total_cash_system')
                    ->numeric(),
                TextEntry::make('total_card_system')
                    ->numeric(),
                TextEntry::make('total_cash_counted')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('difference')
                    ->numeric()
                    ->placeholder('-'),
                IconEntry::make('within_margin')
                    ->boolean()
                    ->placeholder('-'),
                TextEntry::make('margin_threshold')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('observations')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('validated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('digital_signature')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('status'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
