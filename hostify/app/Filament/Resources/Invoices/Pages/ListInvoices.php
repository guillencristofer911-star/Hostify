<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Resources\Invoices\InvoiceResource;
use App\Models\Invoice;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return []; // Las facturas se generan solo al hacer checkout
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'todas';
    }

    public function getTabs(): array
    {
        return [
            'todas' => Tab::make('Todas')
                ->icon('heroicon-o-queue-list')
                ->badge(static fn () => Invoice::count())
                ->deferBadge(),

            'pendiente' => Tab::make('Pendientes')
                ->icon('heroicon-o-clock')
                ->badge(static fn () => Invoice::where('status', 'pendiente')->count())
                ->badgeColor('warning')
                ->deferBadge()
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('status', 'pendiente')
                          ->orderBy('created_at', 'desc')
                ),

            'pagada' => Tab::make('Pagadas')
                ->icon('heroicon-o-check-circle')
                ->badge(static fn () => Invoice::where('status', 'pagada')->count())
                ->badgeColor('success')
                ->deferBadge()
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('status', 'pagada')
                          ->orderBy('updated_at', 'desc')
                ),

            'anulada' => Tab::make('Anuladas')
                ->icon('heroicon-o-x-circle')
                ->badge(static fn () => Invoice::where('status', 'anulada')->count())
                ->badgeColor('danger')
                ->deferBadge()
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('status', 'anulada')
                          ->orderBy('updated_at', 'desc')
                ),
        ];
    }
}