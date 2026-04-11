<?php

namespace App\Filament\Resources\Reservations;

use App\Filament\Resources\Reservations\Pages\CreateReservation;
use App\Filament\Resources\Reservations\Pages\EditReservation;
use App\Filament\Resources\Reservations\Pages\ListReservations;
use App\Filament\Resources\Reservations\Pages\ViewReservation;
use App\Filament\Resources\Reservations\Schemas\ReservationForm;
use App\Filament\Resources\Reservations\Tables\ReservationsTable;
use App\Models\Reservation;
use App\Models\User;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;
    protected static string|BackedEnum|null $navigationIcon  = Heroicon::CalendarDays;
    protected static string|UnitEnum|null   $navigationGroup = 'Operaciones';
    protected static ?string $navigationLabel      = 'Reservas';
    protected static ?string $modelLabel           = 'Reserva';
    protected static ?string $pluralModelLabel     = 'Reservas';
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

    public static function canViewAny(): bool       { return static::hasAccess('view_any_reservation'); }
    public static function canCreate(): bool        { return static::hasAccess('create_reservation'); }
    public static function canEdit($record): bool   { return static::hasAccess('edit_reservation'); }
    public static function canDelete($record): bool { return static::hasAccess('delete_reservation'); }

    public static function form(Schema $schema): Schema
    {
        return ReservationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReservationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListReservations::route('/'),
            'create' => CreateReservation::route('/create'),
            'view'   => ViewReservation::route('/{record}'),
            'edit'   => EditReservation::route('/{record}/edit'),
        ];
    }
}