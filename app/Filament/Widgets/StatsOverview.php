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
                ->icon('heroicon-o-rectangle-stack')
                ->color('info'),

            Stat::make('Active Projects', $activeProjects . ' Projects')
                ->icon('heroicon-o-briefcase')
                ->color('success'),

            Stat::make('Total Clients', $totalClients . ' Clients')
                ->icon('heroicon-o-users')
                ->color('primary'),

            Stat::make('Total Contracted Value', 'IDR ' . number_format($totalContractedValue, 0, ',', '.'))
                ->icon('heroicon-o-currency-dollar')
                ->color('secondary'),

            Stat::make('Total Paid Invoices', 'IDR ' . number_format($totalPaidInvoices, 0, ',', '.'))
                ->icon('heroicon-o-banknotes')
                ->color('success'),
        ];
    }
}
