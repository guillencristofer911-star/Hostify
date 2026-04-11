<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static string|BackedEnum|null $navigationIcon   = Heroicon::UserCircle;
    protected static string|UnitEnum|null   $navigationGroup  = 'Configuración';
    protected static ?string $navigationLabel      = 'Usuarios';
    protected static ?string $modelLabel           = 'Usuario';
    protected static ?string $pluralModelLabel     = 'Usuarios';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?int    $navigationSort       = 3;

    private static function authUser(): ?User
    {
        /** @var User|null $user */
        $user = Auth::user();
        return $user;
    }

    private static function isSuperAdmin(): bool
    {
        try {
            return static::authUser()?->hasRole('Super Admin') ?? false;
        } catch (\Exception) {
            return false;
        }
    }

    public static function canViewAny(): bool   { return static::isSuperAdmin(); }
    public static function canCreate(): bool    { return static::isSuperAdmin(); }
    public static function canEdit($record): bool { return static::isSuperAdmin(); }

    public static function canDelete($record): bool
    {
        if (! static::isSuperAdmin()) return false;
        return static::authUser()?->id !== $record->id;
    }

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit'   => EditUser::route('/{record}/edit'),
        ];
    }
}