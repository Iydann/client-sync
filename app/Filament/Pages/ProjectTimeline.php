<?php

namespace App\Filament\Pages;

use App\Models\Project;
use Carbon\Carbon;
use Filament\Pages\Page;
use Livewire\Attributes\On; // Pastikan ini di-import

class ProjectTimeline extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $title = 'Project Timeline';
    protected string $view = 'filament.pages.project-timeline';

    // Properti public agar bisa diakses view
    public $year;

    public function mount()
    {
        $this->year = request()->integer('year', now()->year);
    }

    // Listener Wajib: Menangkap event dari Global Filter
    #[On('yearChanged')]
    public function updateYear($year)
    {
        $this->year = $year;
        // Tidak perlu $this->js(), kita akan handle via Alpine di Blade
    }

    public function getViewData(): array
    {
        // 1. Query Data
        $query = Project::query()
            ->whereNotNull('start_date')
            ->whereNotNull('deadline');

        // 2. Filter Tahun
        if ($this->year !== 'all') {
            $query->whereYear('contract_date', $this->year);
        }

        $projects = $query->get()
            ->map(fn (Project $project) => $this->transformProjectData($project))
            ->filter()
            ->values();

        return [
            'ganttData' => [
                'data' => $projects->toArray(),
                'links' => [] 
            ],
            'currentFilterYear' => $this->year, 
        ];
    }

    private function transformProjectData(Project $project): ?array
    {
        try {
            $start = Carbon::parse($project->start_date);
            $end = Carbon::parse($project->deadline);
            
            $status = $project->status->value ?? $project->status ?? 'pending';
            $statusLabel = ucwords(str_replace('_', ' ', $status));

            return [
                'id' => $project->id,
                'text' => $project->title ?? 'Untitled',
                'start_date' => $start->format('Y-m-d'), 
                'duration' => max(1, $start->diffInDays($end)),
                'progress' => ($project->progress ?? 0) / 100,
                'status' => $statusLabel,
                'color' => $this->getStatusColor($status),
                'textColor' => '#ffffff'
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    private function getStatusColor(string $status): string
    {
        return match (strtolower($status)) {
            'completed', 'done', 'finished' => '#10b981',
            'pending', 'draft'              => '#9ca3af',
            'cancelled'                     => '#ef4444',
            'in_progress'                   => '#3b82f6',
            default                         => '#3b82f6',
        };
    }
}