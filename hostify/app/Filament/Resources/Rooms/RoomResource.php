<?php

namespace App\Filament\Resources\Rooms;

use App\Filament\Resources\Rooms\Pages\CreateRoom;
use App\Filament\Resources\Rooms\Pages\EditRoom;
use App\Filament\Resources\Rooms\Pages\ListRooms;
use App\Filament\Resources\Rooms\Schemas\RoomForm;
use App\Filament\Resources\Rooms\Tables\RoomsTable;
use App\Models\Room;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::Home;
    protected static string|UnitEnum|null $navigationGroup      = 'Configuración';
    protected static ?string $navigationLabel      = 'Habitaciones';
    protected static ?string $modelLabel           = 'Habitación';
    protected static ?string $pluralModelLabel     = 'Habitaciones';
    protected static ?string $recordTitleAttribute = 'number';
    protected static ?int    $navigationSort       = 2;

    public static function form(Schema $schema): Schema
    {
        return RoomForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RoomsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListRooms::route('/'),
            'create' => CreateRoom::route('/create'),
            'edit'   => EditRoom::route('/{record}/edit'),
        ];
    }
}
