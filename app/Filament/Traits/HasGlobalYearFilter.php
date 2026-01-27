<?php

namespace App\Filament\Traits;

use Livewire\Attributes\On;

trait HasGlobalYearFilter
{
    #[On('yearChanged')]
    public function onYearChanged($year): void
    {
        session(['project_year' => $year]);

        if (property_exists($this, 'year')) {
            $this->year = $year;
        }

        if (method_exists($this, 'resetPage')) {
            $this->resetPage();
        }

        $this->dispatch('$refresh');
    }
}