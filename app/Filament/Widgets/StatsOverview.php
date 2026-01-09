<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Client;
use App\Models\Project;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        // Total Clients
        $totalClients = Client::count();
        
        // Active Projects (in_progress)
        $activeProjects = Project::where('status', 'in_progress')->count();
        $totalProjects = Project::count();
        
        return [
            Stat::make('total_clients', 'Total Clients')
                ->description($totalClients)
                ->icon('heroicon-o-users')
                ->color('primary'),

            Stat::make('active_projects', 'Active Projects')
                ->description($activeProjects)
                ->icon('heroicon-o-briefcase')
                ->color('success'),

            Stat::make('total_projects', 'Total Projects')
                ->description($totalProjects)
                ->icon('heroicon-o-rectangle-stack')
                ->color('info'),
            
        ];
    }
}
