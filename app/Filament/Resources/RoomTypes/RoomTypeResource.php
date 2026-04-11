<?php

namespace App\Filament\Resources\RoomTypes;

use App\Filament\Resources\RoomTypes\Pages\CreateRoomType;
use App\Filament\Resources\RoomTypes\Pages\EditRoomType;
use App\Filament\Resources\RoomTypes\Pages\ListRoomTypes;
use App\Filament\Resources\RoomTypes\Schemas\RoomTypeForm;
use App\Filament\Resources\RoomTypes\Tables\RoomTypesTable;
use App\Models\RoomType;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use UnitEnum;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class RoomTypeResource extends Resource
{
    protected static ?string $model = RoomType::class;
    protected static string|BackedEnum|null $navigationIcon      = Heroicon::Tag;
    protected static string|UnitEnum|null   $navigationGroup     = 'Configuración';
    protected static ?string $navigationLabel      = 'Tipos de Habitación';
    protected static ?string $modelLabel           = 'Tipo de Habitación';
    protected static ?string $pluralModelLabel     = 'Tipos de Habitación';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?int    $navigationSort       = 1;

    private static function authUser(): ?User
    {
        /** @var User|null $user */
        $user = Auth::user();
        return $user;
    }

    private static function hasAccess(string $permission): bool
    {
        try {
            return static::authUser()?->hasPermissionTo($permission) ?? false;
        } catch (\Exception) {
            return false;
        }
    }

    public static function canViewAny(): bool       { return static::hasAccess('view_any_room_type'); }
    public static function canCreate(): bool        { return static::hasAccess('create_room_type'); }
    public static function canEdit($record): bool   { return static::hasAccess('edit_room_type'); }
    public static function canDelete($record): bool { return static::hasAccess('delete_room_type'); }

    public static function form(Schema $schema): Schema
    {
        return RoomTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RoomTypesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListRoomTypes::route('/'),
            'create' => CreateRoomType::route('/create'),
            'edit'   => EditRoomType::route('/{record}/edit'),
        ];
    }
}