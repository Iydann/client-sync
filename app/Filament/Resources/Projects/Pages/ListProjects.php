<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;

class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    // Tambahkan properti untuk menyimpan state tahun
    public $year;

    public function mount(): void
    {
        parent::mount();
        $this->year = request()->integer('year', now()->year);
    }

    /**
     * Mendengarkan event dari Global Filter di AdminPanelProvider
     */
    #[On('yearChanged')]
    public function updateYear($year): void
    {
        $this->year = $year;
        // Reset page ke 1 saat filter berubah agar tidak terjadi error offset
        $this->resetPage();
    }

    /**
     * Override getTableQuery untuk menyuntikkan filter tahun
     */
    protected function getTableQuery(): ?Builder
    {
        $query = parent::getTableQuery();
        
        return $this->applyFiltersToTableQuery($query);
    }

    /**
     * Logika filter dipisahkan agar bersih
     */
    protected function applyFiltersToTableQuery(Builder $query): Builder
    {
        if ($this->year && $this->year !== 'all') {
            $query->whereYear('contract_date', $this->year);
        }

        return $query;
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array 
    {
        return [
            'all' => Tab::make('All Projects'),
            'active' => Tab::make('Active Projects')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['in_progress', 'pending'])),
            'completed' => Tab::make('Completed Projects')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'completed')),
            'cancelled' => Tab::make('Cancelled Projects')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'cancelled')),
        ];
    }
}