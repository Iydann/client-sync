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
        
        // total invoices
        $totalinvoices = Invoice::count();

        return [
            Stat::make('Total Projects', $totalProjects)
                ->icon('heroicon-o-rectangle-stack')
                ->color('info'),

            Stat::make('Active Projects', $activeProjects)
                ->icon('heroicon-o-briefcase')
                ->color('success'),

            Stat::make('Total Clients', $totalClients)
                ->icon('heroicon-o-users')
                ->color('primary'),

            Stat::make('Total Invoices', $totalinvoices)
                ->icon('heroicon-o-document-text')
                ->color('warning'),
        ];
    }
}
