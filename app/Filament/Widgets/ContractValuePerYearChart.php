<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class ContractValuePerYearChart extends ChartWidget
{
    protected ?string $heading = 'Total Contracted Value per Year';

    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return ! Auth::user()?->hasRole('client');
    }

    protected function getData(): array
    {
        $data = Project::query()
            ->selectRaw('YEAR(contract_date) as year, SUM(contract_value) as total')
            ->whereNotNull('contract_date')
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Total Contract Value',
                    'data' => $data->pluck('total'),
                ],
            ],
            'labels' => $data->pluck('year'),
        ];
    }

    protected function getType(): string
    {
        return 'bar'; 
    }
}