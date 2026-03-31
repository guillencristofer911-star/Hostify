<?php

namespace App\Filament\Resources\Invoices;

use App\Filament\Resources\Invoices\Pages\ListInvoices;
use App\Filament\Resources\Invoices\Pages\ViewInvoice;
use App\Filament\Resources\Invoices\Tables\InvoicesTable;
use App\Models\Invoice;
use App\Models\User;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;
    protected static string|BackedEnum|null $navigationIcon  = Heroicon::DocumentText;
    protected static string|UnitEnum|null   $navigationGroup = 'Facturación';
    protected static ?string $navigationLabel      = 'Facturas';
    protected static ?string $modelLabel           = 'Factura';
    protected static ?string $pluralModelLabel     = 'Facturas';
    protected static ?string $recordTitleAttribute = 'invoice_number';
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

    public static function canViewAny(): bool       { return static::hasAccess('view_any_invoice'); }
    public static function canCreate(): bool        { return false; }
    public static function canEdit($record): bool   { return false; }

    public static function canDelete($record): bool
    {
        try {
            return static::authUser()?->hasRole('Super Admin') ?? false;
        } catch (\Exception) {
            return false;
        }
    }

    public static function table(Table $table): Table
    {
        return InvoicesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInvoices::route('/'),
            'view'  => ViewInvoice::route('/{record}'),
        ];
    }
}