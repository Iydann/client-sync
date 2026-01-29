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

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Client::query()->with([
                    'projects' => function ($query) {
                        if ($this->year && $this->year !== 'all') {
                            $query->whereYear('contract_date', $this->year);
                        }
                    },
                    'projects.invoices' => function ($query) {
                        if ($this->year && $this->year !== 'all') {
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

                TextColumn::make('projects_count')
                    ->label('Projects Status')
                    ->counts('projects', function (Builder $query) {
                        if ($this->year && $this->year !== 'all') {
                            $query->whereYear('contract_date', $this->year);
                        }
                        return $query;
                    })
                    ->html()
                    ->formatStateUsing(function ($state, Client $record) {
                        $projects = $record->projects;
                        if ($this->year && $this->year !== 'all') {
                            $projects = $projects->filter(function ($project) {
                                return \Carbon\Carbon::parse($project->contract_date)->year == $this->year;
                            });
                        }

                        $getStatus = fn($p) => $p->status instanceof \BackedEnum ? $p->status->value : $p->status;

                        $completed = $projects->filter(fn($p) => $getStatus($p) === 'completed')->count();
                        $inProgress = $projects->filter(fn($p) => $getStatus($p) === 'in_progress')->count();
                        $pending = $projects->filter(fn($p) => in_array($getStatus($p), ['hold', 'on_hold', 'pending']))->count();
                        $cancelled = $projects->filter(fn($p) => $getStatus($p) === 'cancelled')->count();

                        return "{$completed} Complete, {$inProgress} In Progress, <br> {$pending} Pending, {$cancelled} Cancelled";
                    })
                    ->color('gray')
                    ->size(TextSize::Small) 
                    ->wrap(), 

                TextColumn::make('total_contract')
                    ->label('Contract Value')
                    ->state(fn (Client $record) => $record->projects->sum('contract_value'))
                    ->money('IDR', locale: 'id'),

                TextColumn::make('total_paid')
                    ->label('Paid Revenue')
                    ->state(function (Client $record) {
                        return $record->projects->flatMap->invoices
                            ->where('status', 'paid')
                            ->sum('amount');
                    })
                    ->money('IDR', locale: 'id')
                    ->color('success'),

                TextColumn::make('total_unpaid')
                    ->label('Unpaid Invoices')
                    ->state(function (Client $record) {
                        return $record->projects->flatMap->invoices
                            ->where('status', '!=', 'paid')
                            ->sum('amount');
                    })
                    ->money('IDR', locale: 'id')
                    ->color('danger'),

                TextColumn::make('uninvoiced')
                    ->label('Uninvoiced')
                    ->state(function (Client $record) {
                        $contract = $record->projects->sum('contract_value');
                        $totalInvoiced = $record->projects->flatMap->invoices->sum('amount');
                        return max(0, $contract - $totalInvoiced);
                    })
                    ->money('IDR', locale: 'id')
                    ->color('warning')
                    ->weight(FontWeight::Medium),
            ])
            ->actions([
                Action::make('view_projects')
                    ->label('View Projects')
                    ->icon('heroicon-m-eye')
                    ->modalHeading(fn(Client $record) => "Projects: {$record->client_name}")
                    ->modalWidth('full')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(fn ($action) => $action->label('Close'))
                    ->modalContent(fn (Client $record) => view('filament.pages.partials.modal-wrapper', [
                        'clientId' => $record->id,
                        'year' => $this->year, // Mengirim tahun ke modal utk filter
                    ])),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50]);
    }
}