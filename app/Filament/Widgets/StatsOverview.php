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
        $yearLabel = $this->year === 'all' ? 'All' : (string) $this->year;
        $labelSuffix = " ({$yearLabel})";

        $totalProjects = $this->applyFilters(Project::query(), 'contract_date')->count();
        
        $completedProjects = $this->applyFilters(Project::where('status', 'completed'), 'contract_date')->count();
        $inProgressProjects = $this->applyFilters(Project::where('status', 'in_progress'), 'contract_date')->count();
        $pendingProjects = $this->applyFilters(Project::where('status', 'pending'), 'contract_date')->count();
        $cancelledProjects = $this->applyFilters(Project::where('status', 'cancelled'), 'contract_date')->count();

        $totalContractedValue = $this->applyFilters(Project::query(), 'contract_date')->sum('contract_value');
        $totalPaidInvoices = $this->applyFilters(Invoice::where('status', 'paid'), 'due_date')->sum('amount');
        $totalUnpaidInvoices = $this->applyFilters(Invoice::where('status', 'unpaid'), 'due_date')->sum('amount');

        $totalInvoiced = $totalPaidInvoices + $totalUnpaidInvoices;
        $uninvoicedAmount = max(0, $totalContractedValue - $totalInvoiced);

        $formatRp = fn($num) => 'IDR ' . number_format($num, 0, ',', '.');

        $projectDescription = new HtmlString(sprintf(
            '<div style="width: 100%%; min-width: 280px; display: flex; justify-content: space-between; align-items: flex-start; margin-top: 0.5rem; font-size: 0.95rem; line-height: 1.6;">
                
                <div style="text-align: left;">
                    <div class="text-600">Completed: %s</div>
                    <div class="text-600">Pending: %s</div>
                </div>

                <div style="text-align: left;">
                    <div class="text-600 whitespace-nowrap">In Progress: %s</div>
                    <div class="text-600 whitespace-nowrap">Cancelled: %s</div>
                </div>

            </div>',
            number_format($completedProjects, 0, ',', '.'),
            number_format($pendingProjects, 0, ',', '.'),
            number_format($inProgressProjects, 0, ',', '.'),
            number_format($cancelledProjects, 0, ',', '.')
        ));

        $financeDescription = new HtmlString(sprintf(
            '<div style="margin-top: 0.5rem; font-size: 0.95rem; line-height: 1.6;">
                <span class="text-600">Paid: %s</span><br>
                <span class="text-600">Unpaid: %s</span><br>
                <span class="text-600">Uninvoiced: %s</span>
            </div>',
            number_format($totalPaidInvoices, 0, ',', '.'),
            number_format($totalUnpaidInvoices, 0, ',', '.'),
            number_format($uninvoicedAmount, 0, ',', '.')
        ));
        
        $card1 = Stat::make('Total Projects' . $labelSuffix, $totalProjects)
            ->description($projectDescription)
            ->chart([$completedProjects, $inProgressProjects, $pendingProjects, $cancelledProjects]) 
            ->icon('heroicon-o-rectangle-stack')
            ->color('info');

        $card3 = Stat::make('Total Contract Value' . $labelSuffix, $formatRp($totalContractedValue))
            ->description($financeDescription)
            ->icon('heroicon-o-currency-dollar')
            ->color('success') 
            ->chart([$totalPaidInvoices, $totalInvoiced, $totalContractedValue]);

        if ($isClient) {
            $card2 = Stat::make('Unpaid Invoices' . $labelSuffix, $formatRp($totalUnpaidInvoices))
                ->description('Amount due')
                ->icon('heroicon-o-exclamation-circle')
                ->color($totalUnpaidInvoices > 0 ? 'danger' : 'success')
                ->chart([$totalUnpaidInvoices, $totalPaidInvoices]);
        } else {
            $totalClients = $this->applyFilters(Client::query(), 'created_at')->count();
            $totalIndividualClients = $this->applyFilters(Client::where('client_type', 'individual'), 'created_at')->count();
            $totalCorporateClients = $this->applyFilters(Client::where('client_type', 'corporate'), 'created_at')->count();
            $totalGovernmentClients = $this->applyFilters(Client::where('client_type', 'government'), 'created_at')->count();

            $clientDescription = new HtmlString(sprintf(
                '<div style="margin-top: 0.5rem; font-size: 0.95rem; line-height: 1.6;">
                    <span class="text-600">Individual: %s</span><br>
                    <span class="text-600">Corporate: %s</span><br>
                    <span class="text-600">Government: %s</span>
                </div>',
                number_format($totalIndividualClients, 0, ',', '.'),
                number_format($totalCorporateClients, 0, ',', '.'),
                number_format($totalGovernmentClients, 0, ',', '.')
            ));

            $card2 = Stat::make('Total Clients' . $labelSuffix, $totalClients)
                ->description($clientDescription)
                ->icon('heroicon-o-users')
                ->color('primary')
                ->chart([$totalIndividualClients, $totalCorporateClients, $totalGovernmentClients]);
        }

        return [$card1, $card2, $card3];
    }
}