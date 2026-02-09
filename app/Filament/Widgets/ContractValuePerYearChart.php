<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;

class ContractValuePerYearChart extends ChartWidget
{
    protected ?string $heading = 'Total Contracted Value per Year';

    protected static ?int $sort = 2;

    protected ?string $pollingInterval = null;

    public $year;

    public static function canView(): bool
    {
        return ! Auth::user()?->hasRole('client');
    }

    public function mount(): void
    {
        $this->year = session('project_year', now()->year);
    }

    #[On('yearChanged')]
    public function updateYear($year): void
    {
        $this->year = $year;
        $this->dispatch('$refresh');
    }

    protected function shouldCache(): bool
    {
        return false;
    }

    protected function getData(): array
    {
        if ($this->year && $this->year !== 'all') {
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
                        'label' => 'Total Contract Value',
                        'data' => $values,
                    ],
                ],
                'labels' => $labels,
            ];
        }

        $yearly = Project::query()
            ->selectRaw('YEAR(contract_date) as year, SUM(contract_value) as total')
            ->whereNotNull('contract_date')
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Total Contract Value',
                    'data' => $yearly->pluck('total'),
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