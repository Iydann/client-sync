<?php

namespace App\Filament\Pages;

use App\Models\Client;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;

class ClientInsights extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?string $navigationLabel = 'Client Insights';
    protected static ?string $title = 'Client Insights Overview';
    protected static string|\UnitEnum|null $navigationGroup = 'Analytics';
    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.client-insights';

    public $year;

    public function mount()
    {
        $this->year = request()->integer('year', now()->year);
    }

    #[On('yearChanged')]
    public function updateYear($year)
    {
        $this->year = $year;
        // Tidak perlu resetPage() manual jika menggunakan trait InteractsWithTable secara standar, 
        // tapi jika perlu, bisa ditambahkan $this->resetTable();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Client::query()->with([
                    // Filter Project yang di-load berdasarkan tahun
                    'projects' => function ($query) {
                        if ($this->year !== 'all') {
                            $query->whereYear('contract_date', $this->year);
                        }
                    },
                    // Filter Invoice di dalam project juga
                    'projects.invoices' => function ($query) {
                        if ($this->year !== 'all') {
                            $query->whereYear('due_date', $this->year);
                        }
                    }
                ])
            )
            ->columns([
                TextColumn::make('client_name')
                    ->label('Client Name')
                    ->searchable()
                    ->weight(FontWeight::Bold)
                    ->description(fn (Client $record) => $record->client_type->name ?? '-'),

                // Untuk count, kita harus inject filter ke subquery count-nya
                TextColumn::make('projects_count')
                    ->label('Projects')
                    ->counts('projects', function (Builder $query) {
                        if ($this->year !== 'all') {
                            $query->whereYear('contract_date', $this->year);
                        }
                        return $query;
                    })
                    ->formatStateUsing(function (string $state, Client $record) {
                        // Karena kita sudah memfilter eager loading 'projects' di atas (query utama),
                        // $record->projects di sini SUDAH terfilter tahunnya.
                        $filteredProjects = $record->projects; 
                        
                        $active = $filteredProjects->where('status', 'in_progress')->count(); // Sesuaikan logika status enum/string
                        
                        // $state adalah hasil dari counts(), yang juga sudah kita filter di atas.
                        return "{$state} Total ({$active} Active)";
                    })
                    ->color('gray'),

                TextColumn::make('total_contract')
                    ->label('Contract Value')
                    ->state(function (Client $record) {
                        // $record->projects sudah terfilter tahun di query utama
                        return $record->projects->sum('contract_value');
                    })
                    ->money('IDR', locale: 'id'),

                TextColumn::make('total_paid')
                    ->label('Paid Revenue')
                    ->state(function (Client $record) {
                        // projects dan invoices sudah terfilter tahun
                        return $record->projects->flatMap->invoices
                            ->where('status', 'paid')
                            ->sum('amount');
                    })
                    ->money('IDR', locale: 'id')
                    ->color('success'),

                TextColumn::make('outstanding')
                    ->label('Unpaid & Uninvoiced')
                    ->state(function (Client $record) {
                        $contract = $record->projects->sum('contract_value');
                        $paid = $record->projects->flatMap->invoices
                            ->where('status', 'paid')
                            ->sum('amount');
                        
                        return max(0, $contract - $paid);
                    })
                    ->money('IDR', locale: 'id')
                    ->color('warning')
                    ->weight(FontWeight::Medium),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50]);
    }
}