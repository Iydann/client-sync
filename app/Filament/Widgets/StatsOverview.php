<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Client;
use App\Models\Project;
use App\Models\Invoice;

use Livewire\Attributes\On;

class StatsOverview extends StatsOverviewWidget
{
    protected ?string $pollingInterval = null;

    public $year;

    public function mount()
    {
        $this->year = request('year', now()->year);
    }

    #[On('yearChanged')]
    public function updateYear($year)
    {
        $this->year = $year;
    }

    protected function shouldCache(): bool
    {
        return false;
    }

    protected function getStats(): array
    {

        // Total Clients filtered by created_at year
        $clientsQuery = Client::query();
        if ($this->year !== 'all') {
            $clientsQuery->whereYear('created_at', $this->year);
        }
        $totalClients = $clientsQuery->count();

        // Filter Project by contract_date
        $activeProjectsQuery = Project::where('status', 'in_progress');
        if ($this->year !== 'all') {
            $activeProjectsQuery->whereYear('contract_date', $this->year);
        }
        $activeProjects = $activeProjectsQuery->count();

        $totalProjectsQuery = Project::query();
        if ($this->year !== 'all') {
            $totalProjectsQuery->whereYear('contract_date', $this->year);
        }
        $totalProjects = $totalProjectsQuery->count();

        $contractedValueQuery = Project::query();
        if ($this->year !== 'all') {
            $contractedValueQuery->whereYear('contract_date', $this->year);
        }
        $totalContractedValue = $contractedValueQuery->sum('contract_value');

        // Filter Invoice by contract_date jika ada, atau tetap updated_at
        $paidInvoicesQuery = Invoice::where('status', 'paid');
        if ($this->year !== 'all') {
            $paidInvoicesQuery->whereYear('updated_at', $this->year);
        }
        $totalPaidInvoices = $paidInvoicesQuery->sum('amount');
        return [
            Stat::make('Total Projects', $totalProjects . ' Projects')
                ->description('All projects in the system')
                ->icon('heroicon-o-rectangle-stack')
                ->color('info'),

            Stat::make('Active Projects', $activeProjects . ' Projects')
                ->description('Projects currently in progress')
                ->icon('heroicon-o-briefcase')
                ->color('success'),

            Stat::make('Total Clients', $totalClients . ' Clients')
                ->description('All clients in the system')
                ->icon('heroicon-o-users')
                ->color('primary'),

            Stat::make('Total Contracted Value', 'IDR ' . number_format($totalContractedValue, 0, ',', '.'))
                ->description('Total value of all contracts')
                ->icon('heroicon-o-currency-dollar')
                ->color('secondary'),

            Stat::make('Total Paid Invoices', 'IDR ' . number_format($totalPaidInvoices, 0, ',', '.'))
                ->description('Total amount of paid invoices')
                ->icon('heroicon-o-banknotes')
                ->color('success'),
        ];
    }
}
