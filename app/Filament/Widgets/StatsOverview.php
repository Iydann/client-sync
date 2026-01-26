<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Client;
use App\Models\Project;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use Illuminate\Support\HtmlString;

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

    /**
     * Helper untuk memfilter tahun secara dinamis.
     */
    private function applyDateFilter(Builder $query, string $column = 'created_at'): Builder
    {
        return $this->year === 'all' 
            ? $query 
            : $query->whereYear($column, $this->year);
    }

    protected function getStats(): array
    {
        // 1. Hitung Project & Client
        $totalClients = $this->applyDateFilter(Client::query(), 'created_at')->count();
        $totalProjects = $this->applyDateFilter(Project::query(), 'contract_date')->count();
        
        $activeProjects = Project::where('status', 'in_progress')
            ->when($this->year !== 'all', fn($q) => $q->whereYear('contract_date', $this->year))
            ->count();
        $inactiveProjects = $totalProjects - $activeProjects;

        $totalIndividualClients = $this->applyDateFilter(Client::where('client_type', 'individual'), 'created_at')->count();
        $totalOrganizationClients = $this->applyDateFilter(Client::where('client_type', 'organization'), 'created_at')->count();

        // 2. Hitung Keuangan
        $totalContractedValue = $this->applyDateFilter(Project::query(), 'contract_date')->sum('contract_value');
        $totalPaidInvoices = $this->applyDateFilter(Invoice::where('status', 'paid'), 'due_date')->sum('amount');
        $totalUnpaidInvoices = $this->applyDateFilter(Invoice::where('status', 'unpaid'), 'due_date')->sum('amount');

        // 3. Hitung Uninvoiced (Sisa Kontrak)
        $totalInvoiced = $totalPaidInvoices + $totalUnpaidInvoices;
        $uninvoicedAmount = max(0, $totalContractedValue - $totalInvoiced);

        // Formatter Mata Uang
        $formatRp = fn($num) => 'IDR ' . number_format($num, 0, ',', '.');


        $projectDescription = new HtmlString(sprintf(
            '<div style="margin-top: 0.5rem; font-size: 0.95rem; line-height: 1.6;">
                <span class="text-info-600">Active Projects: %s</span><br>
                <span class="text-info-600">Inactive Projects: %s</span>
            </div>',
            number_format($activeProjects, 0, ',', '.'),
            number_format($inactiveProjects, 0, ',', '.')
        ));
        $clientDescription = new HtmlString(sprintf(
            '<div style="margin-top: 0.5rem; font-size: 0.95rem; line-height: 1.6;">
                <span class="text-primary-600">Individual: %s</span><br>
                <span class="text-primary-600">Organization: %s</span>
            </div>',
            number_format($totalIndividualClients, 0, ',', '.'),
            number_format($totalOrganizationClients, 0, ',', '.')
        ));
        $financeDescription = new HtmlString(sprintf(
            '<div style="margin-top: 0.5rem; font-size: 0.95rem; line-height: 1.6;">
                <span class="text-success-600">Paid: %s</span><br>
                <span class="text-danger-600">Unpaid: %s</span><br>
                <span class="text-warning-600">Uninvoiced: %s</span>
            </div>',
            number_format($totalPaidInvoices, 0, ',', '.'),
            number_format($totalUnpaidInvoices, 0, ',', '.'),
            number_format($uninvoicedAmount, 0, ',', '.')
        ));

        return [
            // Card 1: Projects
            Stat::make('Total Projects', $totalProjects)
                ->description($projectDescription)
                ->chart([$totalProjects, $activeProjects]) 
                ->icon('heroicon-o-rectangle-stack')
                ->color('info'),

            // Card 2: Clients
            Stat::make('Total Clients', $totalClients)
                ->description($clientDescription)
                ->icon('heroicon-o-users')
                ->color('primary')
                ->chart([$totalIndividualClients, $totalOrganizationClients]),

            // Card 3: Financial Summary (ALL IN ONE)
            Stat::make('Total Contract Value', $formatRp($totalContractedValue))
                ->description($financeDescription) // Semua info masuk di sini
                ->icon('heroicon-o-currency-dollar')
                ->color('success') 
                ->chart([$totalPaidInvoices, $totalInvoiced, $totalContractedValue]), 
        ];
    }
}