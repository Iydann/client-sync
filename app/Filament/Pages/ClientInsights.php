<?php

namespace App\Filament\Pages;

use App\Models\Client;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action; // Tambahan wajib untuk Action
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
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
                    ->label('Projects')
                    ->counts('projects', function (Builder $query) {
                        if ($this->year && $this->year !== 'all') {
                            $query->whereYear('contract_date', $this->year);
                        }
                        return $query;
                    })
                    ->formatStateUsing(function (string $state, Client $record) {
                        $filteredProjects = $record->projects; 
                        $active = $filteredProjects->where('status', 'in_progress')->count(); 
                        
                        return "{$state} Total ({$active} Active)";
                    })
                    ->color('gray'),

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

                // KOLOM BARU 1: UNPAID (Tagihan sudah terbit, tapi belum dibayar)
                TextColumn::make('total_unpaid')
                    ->label('Unpaid Invoices')
                    ->state(function (Client $record) {
                        return $record->projects->flatMap->invoices
                            ->where('status', '!=', 'paid') // Asumsi status selain paid adalah hutang
                            ->sum('amount');
                    })
                    ->money('IDR', locale: 'id')
                    ->color('danger'),

                // KOLOM BARU 2: UNINVOICED (Sisa kontrak yang belum dibuatkan invoice sama sekali)
                TextColumn::make('uninvoiced')
                    ->label('Uninvoiced')
                    ->state(function (Client $record) {
                        $contract = $record->projects->sum('contract_value');
                        // Total Invoiced = Paid + Unpaid (Semua invoice yang ada)
                        $totalInvoiced = $record->projects->flatMap->invoices->sum('amount');
                        
                        return max(0, $contract - $totalInvoiced);
                    })
                    ->money('IDR', locale: 'id')
                    ->color('warning')
                    ->weight(FontWeight::Medium),
            ])
            ->actions([
                // ACTION BARU: View Projects List
                Action::make('view_projects')
                    ->label('View Projects')
                    ->icon('heroicon-m-eye')
                    ->modalHeading(fn(Client $record) => "Projects: {$record->client_name}")
                    ->modalSubmitAction(false) // View only, matikan tombol submit
                    ->modalCancelAction(fn ($action) => $action->label('Close'))
                    ->modalContent(fn (Client $record) => view('filament.pages.partials.client-projects-list', [
                        'projects' => $record->projects, // Menggunakan projects yang sudah di-filter di query utama
                    ])),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50]);
    }
}