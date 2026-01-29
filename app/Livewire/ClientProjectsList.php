<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\Invoice;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\Enums\FontWeight;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Livewire\Component;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Summarizer;

class ClientProjectsList extends Component implements HasForms, HasTable, HasActions
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;

    public $clientId;
    public $year;
    
    public function mount($clientId, $year = null)
    {
        $this->clientId = $clientId;
        $this->year = $year;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Project::query()
                    ->where('client_id', $this->clientId)
                    ->when($this->year && $this->year !== 'all', function ($query) {
                        return $query->whereYear('contract_date', $this->year);
                    })
                    ->with(['invoices']) 
            )
            ->columns([
                TextColumn::make('title')
                    ->label('Project Name')
                    ->description(fn (Project $record) => $record->contract_date ? \Carbon\Carbon::parse($record->contract_date)->format('d M Y') : '-')
                    ->weight(FontWeight::Medium),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state): string => match ($state->value ?? $state) {
                        'in_progress' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        'hold', 'on_hold' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(function ($state) {
                        if ($state instanceof \Filament\Support\Contracts\HasLabel) return $state->getLabel();
                        if ($state instanceof \BackedEnum) return ucfirst(str_replace('_', ' ', $state->value));
                        return ucfirst((string) $state);
                    }),

                TextColumn::make('contract_value')
                    ->label('Contract')
                    ->money('IDR', locale: 'id')
                    ->alignEnd()
                    ->summarize(Sum::make()->money('IDR', locale: 'id')->label('Total')),

                TextColumn::make('paid_revenue')
                    ->label('Paid')
                    ->state(fn (Project $record) => $record->invoices->where('status', 'paid')->sum('amount'))
                    ->money('IDR', locale: 'id')
                    ->color('success')
                    ->alignEnd()
                    ->summarize(Summarizer::make()
                        ->label('Total')
                        ->money('IDR', locale: 'id')
                        ->using(fn ($query) => Invoice::query()
                            ->whereIn('project_id', $query->clone()->reorder()->select('projects.id'))
                            ->where('status', 'paid')
                            ->sum('amount')
                        )
                    ),

                TextColumn::make('unpaid_invoiced')
                    ->label('Unpaid (Inv)')
                    ->state(fn (Project $record) => $record->invoices->where('status', '!=', 'paid')->sum('amount'))
                    ->money('IDR', locale: 'id')
                    ->color('danger')
                    ->alignEnd()
                    ->summarize(Summarizer::make()
                        ->label('Total')
                        ->money('IDR', locale: 'id')
                        ->using(fn ($query) => Invoice::query()
                            ->whereIn('project_id', $query->clone()->reorder()->select('projects.id'))
                            ->where('status', '!=', 'paid')
                            ->sum('amount')
                        )
                    ),

                TextColumn::make('uninvoiced')
                    ->label('Uninvoiced')
                    ->state(function (Project $record) {
                        $totalInvoiced = $record->invoices->sum('amount');
                        return max(0, $record->contract_value - $totalInvoiced);
                    })
                    ->money('IDR', locale: 'id')
                    ->color('warning')
                    ->alignEnd()
                    ->summarize(Summarizer::make()
                        ->label('Total')
                        ->money('IDR', locale: 'id')
                        ->using(function ($query) {
                            return Project::query()
                                ->whereIn('id', $query->clone()->reorder()->select('projects.id'))
                                ->with('invoices')
                                ->get()
                                ->sum(function ($project) {
                                    $totalInvoiced = $project->invoices->sum('amount');
                                    return max(0, $project->contract_value - $totalInvoiced);
                                });
                        })
                    ),
            ])
            ->paginated(false);
    }

    public function render()
    {
        return view('livewire.client-projects-list');
    }
}