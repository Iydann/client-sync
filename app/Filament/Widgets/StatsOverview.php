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
        $totalClients = Client::whereYear('created_at', $this->year)->count();

        // Filter Project by contract_date
        $activeProjects = Project::where('status', 'in_progress')->whereYear('contract_date', $this->year)->count();
        $totalProjects = Project::whereYear('contract_date', $this->year)->count();
        $totalContractedValue = Project::whereYear('contract_date', $this->year)->sum('contract_value');

        // Filter Invoice by contract_date jika ada, atau tetap updated_at
        $totalPaidInvoices = Invoice::where('status', 'paid')->whereYear('updated_at', $this->year)->sum('amount');
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