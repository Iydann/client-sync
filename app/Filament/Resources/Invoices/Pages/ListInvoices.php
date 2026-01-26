<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Resources\Invoices\InvoiceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    public $year;

    public function mount(): void
    {
        parent::mount();
        $this->year = request()->integer('year', now()->year);
    }

    #[On('yearChanged')]
    public function updateYear($year): void
    {
        $this->year = $year;
        $this->resetPage();
    }

    protected function getTableQuery(): ?Builder
    {
        $query = parent::getTableQuery();

        if ($this->year && $this->year !== 'all') {
            // Asumsi filter berdasarkan due_date atau created_at
            $query->whereYear('due_date', $this->year);
        }

        return $query;
    }

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