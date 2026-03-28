<?php

namespace App\Filament\Resources\ShiftCloses;

use App\Filament\Resources\ShiftCloses\Pages\CreateShiftClose;
use App\Filament\Resources\ShiftCloses\Pages\ListShiftCloses;
use App\Filament\Resources\ShiftCloses\Pages\ViewShiftClose;
use App\Filament\Resources\ShiftCloses\Schemas\ShiftCloseForm;
use App\Filament\Resources\ShiftCloses\Tables\ShiftClosesTable;
use App\Models\ShiftClose;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ShiftCloseResource extends Resource
{
    protected static ?string $model = ShiftClose::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Calculator;
    protected static string|UnitEnum|null  $navigationGroup = 'Facturación';
    protected static ?string $navigationLabel               = 'Cierres de turno';
    protected static ?string $modelLabel                    = 'Cierre de turno';
    protected static ?string $pluralModelLabel              = 'Cierres de turno';
    protected static ?string $recordTitleAttribute          = 'id';
    protected static ?int    $navigationSort                = 2;

    public static function form(Schema $schema): Schema
    {
        return ShiftCloseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ShiftClosesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListShiftCloses::route('/'),
            'create' => CreateShiftClose::route('/create'),
            'view'   => ViewShiftClose::route('/{record}'),
        ];
    }
}