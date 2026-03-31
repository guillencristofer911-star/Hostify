<?php

namespace App\Filament\Resources\CleaningSessions;

use App\Enums\CleaningStatus;
use App\Filament\Resources\CleaningSessions\Pages\CreateCleaningSession;
use App\Filament\Resources\CleaningSessions\Pages\EditCleaningSession;
use App\Filament\Resources\CleaningSessions\Pages\ListCleaningSessions;
use App\Filament\Resources\CleaningSessions\Pages\ViewCleaningSession;
use App\Filament\Resources\CleaningSessions\Schemas\CleaningSessionForm;
use App\Filament\Resources\CleaningSessions\Tables\CleaningSessionsTable;
use App\Models\CleaningSession;
use App\Models\User;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class CleaningSessionResource extends Resource
{
    protected static ?string $model = CleaningSession::class;
    protected static string|BackedEnum|null $navigationIcon  = Heroicon::Sparkles;
    protected static string|UnitEnum|null   $navigationGroup = 'Operaciones';
    protected static ?string $navigationLabel      = 'Limpiezas';
    protected static ?string $modelLabel           = 'Sesión de limpieza';
    protected static ?string $pluralModelLabel     = 'Sesiones de limpieza';
    protected static ?string $recordTitleAttribute = 'id';
    protected static ?int    $navigationSort       = 3;

    //  Helpers de autenticación 

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

    //  Permisos 

    public static function canViewAny(): bool       { return static::hasAccess('view_any_cleaning_session'); }
    public static function canCreate(): bool        { return static::hasAccess('create_cleaning_session'); }
    public static function canEdit($record): bool   { return static::hasAccess('edit_cleaning_session'); }
    public static function canDelete($record): bool { return static::hasAccess('delete_cleaning_session'); }

    //  Badge de navegación (RF-17) 

    public static function getNavigationBadge(): ?string
    {
        /** @var User|null $user */
        $user = static::authUser();
        if (! $user) return null;

        $query = CleaningSession::query()
            ->whereDate('assigned_date', today())
            ->where('status', CleaningStatus::Pendiente->value);

        if ($user->hasRole('housekeeper')) {
            $query->where('assigned_to', $user->id);
        }

        $count = $query->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    //  Form / Table 

    public static function form(Schema $schema): Schema
    {
        return CleaningSessionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CleaningSessionsTable::configure($table);
    }

    //  Páginas 

    public static function getPages(): array
    {
        return [
            'index'  => ListCleaningSessions::route('/'),
            'create' => CreateCleaningSession::route('/create'),
            'view'   => ViewCleaningSession::route('/{record}'),
            'edit'   => EditCleaningSession::route('/{record}/edit'),
        ];
    }
}