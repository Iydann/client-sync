<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Project;

class ContractValuePerYearChart extends ChartWidget
{
    protected ?string $heading = 'Total Contracted Value per Year';

    protected static ?int $sort = 2;

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
        return 'bar'; // bisa: line | bar | area
    }
}
