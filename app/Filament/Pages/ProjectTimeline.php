<?php

namespace App\Filament\Pages;

use App\Models\Project;
use Carbon\Carbon;
use Filament\Pages\Page;
use Livewire\Attributes\On;
use App\Filament\Traits\HasGlobalYearFilter; 

class ProjectTimeline extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $title = 'Project Timeline';
    protected string $view = 'filament.pages.project-timeline';

    use HasGlobalYearFilter;

    public function getViewData(): array
    {
        $year = session('project_year', now()->year);

        $query = Project::query()
            ->whereNotNull('start_date')
            ->whereNotNull('deadline');

        if ($year !== 'all') {
            $query->whereYear('contract_date', $year);
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
            'currentFilterYear' => $year, 
        ];
    }

    private function transformProjectData(Project $project): ?array
    {
        try {
            $start = Carbon::parse($project->start_date);
            $end = Carbon::parse($project->deadline);
            
            $status = $project->status instanceof \UnitEnum 
                ? $project->status->value 
                : ($project->status ?? 'pending');
                
            $statusLabel = ucwords(str_replace('_', ' ', $status));

            $duration = max(1, $start->diffInDays($end) + 1);

            return [
                'id' => $project->id,
                'text' => $project->title ?? 'Untitled',
                
                'start_date' => $start->format('Y-m-d'), 
                'deadline' => $end->format('Y-m-d'),
                
                'duration' => $duration,
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