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

    // ─── Helper autenticación ────────────────────────────────────

    private static function authUser(): ?User
    {
        /** @var User|null $user */
        $user = Auth::user();
        return $user;
    }

    //  Roles con acceso al módulo 

    private static function canAccessModule(): bool
    {
        $user = static::authUser();
        if (! $user) return false;

        return $user->hasAnyRole(['Super Admin', 'supervisor', 'recepcionista', 'camarera']);
    }

    private static function canManage(): bool
    {
        $user = static::authUser();
        if (! $user) return false;

        // Solo Super Admin y supervisor pueden crear/editar/eliminar
        return $user->hasAnyRole(['Super Admin', 'supervisor']);
    }

    //  Permisos por acción 

    public static function canViewAny(): bool       { return static::canAccessModule(); }
    public static function canCreate(): bool        { return static::canManage(); }
    public static function canEdit($record): bool   { return static::canManage(); }
    public static function canDelete($record): bool { return static::canManage(); }

    //  Badge de navegación 

    public static function getNavigationBadge(): ?string
    {
        $user = static::authUser();
        if (! $user) return null;

        $query = CleaningSession::query()
            ->whereDate('assigned_date', today())
            ->where('status', CleaningStatus::Pendiente->value);

        // Camarera solo ve su propio contador (RF-17)
        if ($user->hasRole('camarera')) {
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