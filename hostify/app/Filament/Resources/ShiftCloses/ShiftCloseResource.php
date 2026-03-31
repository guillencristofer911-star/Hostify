<?php

namespace App\Filament\Resources\ShiftCloses;

use App\Filament\Resources\ShiftCloses\Pages\CreateShiftClose;
use App\Filament\Resources\ShiftCloses\Pages\ListShiftCloses;
use App\Filament\Resources\ShiftCloses\Pages\ViewShiftClose;
use App\Filament\Resources\ShiftCloses\Schemas\ShiftCloseForm;
use App\Filament\Resources\ShiftCloses\Tables\ShiftClosesTable;
use App\Models\ShiftClose;
use App\Models\User;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ShiftCloseResource extends Resource
{
    protected static ?string $model = ShiftClose::class;
    protected static string|BackedEnum|null $navigationIcon  = Heroicon::Calculator;
    protected static string|UnitEnum|null   $navigationGroup = 'Facturación';
    protected static ?string $navigationLabel      = 'Cierres de turno';
    protected static ?string $modelLabel           = 'Cierre de turno';
    protected static ?string $pluralModelLabel     = 'Cierres de turno';
    protected static ?string $recordTitleAttribute = 'id';
    protected static ?int    $navigationSort       = 2;

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

    public static function canViewAny(): bool       { return static::hasAccess('view_any_shift_close'); }
    public static function canCreate(): bool        { return static::hasAccess('create_shift_close'); }
    public static function canEdit($record): bool   { return false; }

    public static function canDelete($record): bool
    {
        try {
            return static::authUser()?->hasRole('Super Admin') ?? false;
        } catch (\Exception) {
            return false;
        }
    }

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