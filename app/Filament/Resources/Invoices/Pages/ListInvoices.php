<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Resources\Invoices\InvoiceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use App\Filament\Traits\HasGlobalYearFilter;

use function Symfony\Component\String\s;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    use HasGlobalYearFilter;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array {
        return [
            'all' => Tab::make('All Invoices'),
            'unpaid' => Tab::make('Unpaid Invoices')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'unpaid')),
            'paid' => Tab::make('Paid Invoices')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'paid')),
            'cancelled' => Tab::make('Cancelled Invoices')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'cancelled')),
        ];
    }
}