<?php

namespace App\Filament\Resources\Invoices;

use App\Filament\Resources\Invoices\Pages\ListInvoices;
use App\Filament\Resources\Invoices\Pages\ViewInvoice;
use App\Filament\Resources\Invoices\Tables\InvoicesTable;
use App\Models\Invoice;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static string|BackedEnum|null $navigationIcon  = Heroicon::DocumentText;
    protected static string|UnitEnum|null  $navigationGroup  = 'Facturación';
    protected static ?string $navigationLabel                 = 'Facturas';
    protected static ?string $modelLabel                      = 'Factura';
    protected static ?string $pluralModelLabel                = 'Facturas';
    protected static ?string $recordTitleAttribute            = 'invoice_number';
    protected static ?int    $navigationSort                  = 1;

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