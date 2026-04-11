<?php

namespace App\Filament\Resources\Guests;

use App\Filament\Resources\Guests\Pages\CreateGuest;
use App\Filament\Resources\Guests\Pages\EditGuest;
use App\Filament\Resources\Guests\Pages\ListGuests;
use App\Filament\Resources\Guests\Schemas\GuestForm;
use App\Filament\Resources\Guests\Tables\GuestsTable;
use App\Models\Guest;
use App\Models\User;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class GuestResource extends Resource
{
    protected static ?string $model = Guest::class;
    protected static string|BackedEnum|null $navigationIcon  = Heroicon::Users;
    protected static string|UnitEnum|null   $navigationGroup = 'Operaciones';
    protected static ?string $navigationLabel      = 'Huéspedes';
    protected static ?string $modelLabel           = 'Huésped';
    protected static ?string $pluralModelLabel     = 'Huéspedes';
    protected static ?string $recordTitleAttribute = 'full_name';
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

    public static function canViewAny(): bool       { return static::hasAccess('view_any_guest'); }
    public static function canCreate(): bool        { return static::hasAccess('create_guest'); }
    public static function canEdit($record): bool   { return static::hasAccess('edit_guest'); }
    public static function canDelete($record): bool { return static::hasAccess('delete_guest'); }

    public static function form(Schema $schema): Schema
    {
        return GuestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GuestsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListGuests::route('/'),
            'create' => CreateGuest::route('/create'),
            'edit'   => EditGuest::route('/{record}/edit'),
        ];
    }
}