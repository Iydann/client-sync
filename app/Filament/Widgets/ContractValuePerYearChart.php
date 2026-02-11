<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use Illuminate\Contracts\Support\Htmlable;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class ContractValuePerYearChart extends ChartWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 2;

    protected ?string $pollingInterval = null;

    public string|int $year;

    public function mount(): void
    {
        $this->year = session('project_year', 'all');
    }

    #[On('yearChanged')]
    public function updateYear($year): void
    {
        $this->year = $year ?: 'all';
        $this->dispatch('$refresh');
    }

    protected function shouldCache(): bool
    {
        return false;
    }

    public function getHeading(): string | Htmlable
    {
        $year = $this->year ?? 'all';

        return $year === 'all'
            ? 'Total Contract Value per Year (All)'
            : "Total Contract Value per Month ({$year})";
    }


    protected function getData(): array
    {
        // ===== MONTHLY (specific year) =====
        if ($this->year !== 'all') {
            $monthly = Project::query()
                ->selectRaw('MONTH(contract_date) as month, SUM(contract_value) as total')
                ->whereNotNull('contract_date')
                ->whereYear('contract_date', $this->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->keyBy('month');

            $labels = [];
            $values = [];

            for ($month = 1; $month <= 12; $month++) {
                $labels[] = Carbon::create()->month($month)->format('M');
                $values[] = (float) ($monthly->get($month)->total ?? 0);
            }

            return [
                'datasets' => [
                    [
                        'label' => "Monthly Contract Value ({$this->year})",
                        'data' => $values,
                    ],
                ],
                'labels' => $labels,
            ];
        }

        // ===== YEARLY (all years) =====
        $yearly = Project::query()
            ->selectRaw('YEAR(contract_date) as year, SUM(contract_value) as total')
            ->whereNotNull('contract_date')
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Annual Contract Value',
                    'data' => $yearly->pluck('total')->map(fn ($v) => (float) $v),
                ],
            ],
            'labels' => $yearly->pluck('year'),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
