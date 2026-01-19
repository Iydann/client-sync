<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Client;
use App\Models\Project;
use App\Models\Invoice;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        // Total Clients
        $totalClients = Client::count();
        
        // Active Projects (in_progress)
        $activeProjects = Project::where('status', 'in_progress')->count();
        $totalProjects = Project::count();

        $totalContractedValue = Project::sum('contract_value');
        
        $totalPaidInvoices = Invoice::where('status', 'paid')->sum('amount');

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
