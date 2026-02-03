<?php

namespace App\Filament\Pages;

use App\Models\Client;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Traits\HasGlobalYearFilter;
use Illuminate\Support\Facades\Auth;

class ClientInsights extends Page implements HasTable
{
    use InteractsWithTable;
    use HasGlobalYearFilter;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?string $navigationLabel = 'Client Insights';
    protected static ?string $title = 'Client Insights Overview';
    protected static string|\UnitEnum|null $navigationGroup = 'Analytics';
    protected static ?int $navigationSort = 2;
    protected string $view = 'filament.pages.client-insights';

    public static function canAccess(): bool
    {
        return !Auth::user()?->hasRole('client');
    }

    public $year;

    public function mount(): void
    {
        $this->year = session('project_year', now()->year);
    }

    // Force reset table when year PanelRenderHook changes
    public function updatedYear(): void 
    {
        $this->resetTable(); 
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordAction('view_details')
            ->query(fn () => 
                Client::query()
                    ->whereHas('projects', function (Builder $query) {
                        if ($this->year && $this->year !== 'all') {
                            $query->whereYear('contract_date', $this->year);
                        }
                    })
                    ->addSelect([
                        'total_contract' => \App\Models\Project::query()
                            ->selectRaw('COALESCE(SUM(contract_value), 0)')
                            ->whereColumn('client_id', 'clients.id')
                            ->when($this->year && $this->year !== 'all', fn($q) => $q->whereYear('contract_date', $this->year)),
                    ])
                    ->addSelect([
                        'total_projects' => \App\Models\Project::query()
                            ->selectRaw('COUNT(*)')
                            ->whereColumn('client_id', 'clients.id')
                            ->when($this->year && $this->year !== 'all', fn($q) => $q->whereYear('contract_date', $this->year)),
                    ])
                    ->addSelect([
                        'total_paid' => \App\Models\Invoice::query()
                            ->selectRaw('COALESCE(SUM(invoices.amount), 0)')
                            ->join('projects', 'invoices.project_id', '=', 'projects.id')
                            ->whereColumn('projects.client_id', 'clients.id')
                            ->where('invoices.status', 'paid')
                            ->when($this->year && $this->year !== 'all', function($q) {
                                $q->whereYear('invoices.due_date', $this->year)
                                  ->whereYear('projects.contract_date', $this->year);
                            }),
                    ])
                    ->addSelect([
                        'total_unpaid' => \App\Models\Invoice::query()
                            ->selectRaw('COALESCE(SUM(invoices.amount), 0)')
                            ->join('projects', 'invoices.project_id', '=', 'projects.id')
                            ->whereColumn('projects.client_id', 'clients.id')
                            ->where('invoices.status', 'unpaid') // Strict Filter
                            ->when($this->year && $this->year !== 'all', function($q) {
                                $q->whereYear('invoices.due_date', $this->year)
                                  ->whereYear('projects.contract_date', $this->year);
                            }),
                    ])
                    ->addSelect([
                        'uninvoiced' => \App\Models\Project::query()
                            ->selectRaw('
                                COALESCE(SUM(
                                    GREATEST(0, 
                                        contract_value - COALESCE((
                                            SELECT SUM(amount)
                                            FROM invoices
                                            WHERE invoices.project_id = projects.id
                                            AND invoices.status != \'cancelled\'
                                            ' . ($this->year && $this->year !== 'all' ? "AND YEAR(invoices.due_date) = ".(int)$this->year : "") . '
                                        ), 0)
                                    )
                                ), 0)
                            ')
                            ->whereColumn('client_id', 'clients.id')
                            ->when($this->year && $this->year !== 'all', fn($q) => $q->whereYear('contract_date', $this->year)),
                    ])
            )
            ->columns([
                TextColumn::make('client_name')
                    ->label('Client Name')
                    ->searchable()
                    ->weight(FontWeight::Bold)
                    ->sortable(),

                TextColumn::make('total_projects')
                    ->label('Total Projects')
                    ->sortable(),

                TextColumn::make('total_contract')
                    ->label('Contract Value')
                    ->sortable()
                    ->money('IDR', locale: 'id'),

                TextColumn::make('total_paid')
                    ->label('Paid Invoices')
                    ->sortable()
                    ->money('IDR', locale: 'id')
                    ->color('success'),

                TextColumn::make('total_unpaid')
                    ->label('Unpaid Invoices')
                    ->sortable()
                    ->money('IDR', locale: 'id')
                    ->color('danger'),

                TextColumn::make('uninvoiced')
                    ->label('Uninvoiced')
                    ->sortable()
                    ->money('IDR', locale: 'id')
                    ->color('warning')
                    ->weight(FontWeight::Medium),
            ])
            ->actions([
                Action::make('view_details')
                    ->label('View Details')
                    ->icon('heroicon-m-eye')
                    ->modalHeading(fn(Client $record) => "Projects: {$record->client_name}")
                    ->modalWidth('6xl')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(fn ($action) => $action->label('Close'))
                    ->modalContent(fn (Client $record) => view('filament.pages.partials.modal-wrapper', [
                        'clientId' => $record->id,
                        'year' => $this->year,
                    ])),
            ])
            ->defaultSort('client_name', 'asc')
            ->paginated([10, 25, 50]);
    }
}