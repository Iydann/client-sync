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
use Illuminate\Support\Facades\Auth;

class StatsOverview extends StatsOverviewWidget
{
    protected ?string $pollingInterval = null;

    public $year;

    public function mount()
    {
        $this->year = session('project_year', now()->year);
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
     * Helper Sakti: Filter Tahun + Filter Client (Otomatis)
     */
    private function applyFilters(Builder $query, string $dateColumn = 'created_at'): Builder
    {
        $user = Auth::user();
        
        // 1. Jika Role Client, filter data milik dia saja
        if ($user && $user->hasRole('client')) {
            $model = $query->getModel();
            
            if ($model instanceof Project) {
                $query->where('client_id', $user->client?->id);
            } elseif ($model instanceof Invoice) {
                $query->whereHas('project', fn($q) => $q->where('client_id', $user->client?->id));
            } elseif ($model instanceof Client) {
                $query->where('id', $user->client?->id);
            }
        }

        // 2. Filter Tahun (Logika Lama Anda)
        return $this->year === 'all' 
            ? $query 
            : $query->whereYear($dateColumn, $this->year);
    }

    protected function getStats(): array
    {
        // Cek apakah user adalah Client
        $isClient = Auth::user()?->hasRole('client');

        $totalProjects = $this->applyFilters(Project::query(), 'contract_date')->count();
        
        $activeProjectsQuery = Project::where('status', 'in_progress');
        if ($isClient) {
            $activeProjectsQuery->where('client_id', Auth::user()->client?->id);
        }
        $activeProjects = $activeProjectsQuery
            ->when($this->year !== 'all', fn($q) => $q->whereYear('contract_date', $this->year))
            ->count();
            
        $inactiveProjects = $totalProjects - $activeProjects;

        $totalContractedValue = $this->applyFilters(Project::query(), 'contract_date')->sum('contract_value');
        $totalPaidInvoices = $this->applyFilters(Invoice::where('status', 'paid'), 'due_date')->sum('amount');
        $totalUnpaidInvoices = $this->applyFilters(Invoice::where('status', 'unpaid'), 'due_date')->sum('amount');

        $totalInvoiced = $totalPaidInvoices + $totalUnpaidInvoices;
        $uninvoicedAmount = max(0, $totalContractedValue - $totalInvoiced);

        $formatRp = fn($num) => 'IDR ' . number_format($num, 0, ',', '.');

        $projectDescription = new HtmlString(sprintf(
            '<div style="margin-top: 0.5rem; font-size: 0.95rem; line-height: 1.6;">
                <span class="text-info-600">Active: %s</span><br>
                <span class="text-gray-500">Inactive: %s</span>
            </div>',
            number_format($activeProjects, 0, ',', '.'),
            number_format($inactiveProjects, 0, ',', '.')
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
        
        $card1 = Stat::make('Total Projects', $totalProjects)
            ->description($projectDescription)
            ->chart([$totalProjects, $activeProjects]) 
            ->icon('heroicon-o-rectangle-stack')
            ->color('info');

        $card3 = Stat::make('Total Contract Value', $formatRp($totalContractedValue))
            ->description($financeDescription)
            ->icon('heroicon-o-currency-dollar')
            ->color('success') 
            ->chart([$totalPaidInvoices, $totalInvoiced, $totalContractedValue]);

        if ($isClient) {
            $card2 = Stat::make('Unpaid Invoices', $formatRp($totalUnpaidInvoices))
                ->description('Amount due')
                ->icon('heroicon-o-exclamation-circle')
                ->color($totalUnpaidInvoices > 0 ? 'danger' : 'success')
                ->chart([$totalUnpaidInvoices, $totalPaidInvoices]);
        } else {
            $totalClients = $this->applyFilters(Client::query(), 'created_at')->count();
            $totalIndividualClients = $this->applyFilters(Client::where('client_type', 'individual'), 'created_at')->count();
            $totalOrganizationClients = $this->applyFilters(Client::where('client_type', 'organization'), 'created_at')->count();

            $clientDescription = new HtmlString(sprintf(
                '<div style="margin-top: 0.5rem; font-size: 0.95rem; line-height: 1.6;">
                    <span class="text-primary-600">Individual: %s</span><br>
                    <span class="text-primary-600">Organization: %s</span>
                </div>',
                number_format($totalIndividualClients, 0, ',', '.'),
                number_format($totalOrganizationClients, 0, ',', '.')
            ));

            $card2 = Stat::make('Total Clients', $totalClients)
                ->description($clientDescription)
                ->icon('heroicon-o-users')
                ->color('primary')
                ->chart([$totalIndividualClients, $totalOrganizationClients]);
        }

        return [$card1, $card2, $card3];
    }
}