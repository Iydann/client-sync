<?php

namespace App\Filament\Pages;

use App\Models\Project;
use Carbon\Carbon;
use Filament\Pages\Page;

class ProjectTimeline extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $title = 'Project Timeline';
    protected string $view = 'filament.pages.project-timeline';

    public function getViewData(): array
    {
        $projects = Project::query()
            ->whereNotNull('start_date')
            ->whereNotNull('deadline')
            ->get()
            ->map(function (Project $project) {
                return $this->transformProjectData($project);
            })
            ->filter()
            ->values();

        return [
            'ganttData' => [
                'data' => $projects->toArray(),
                'links' => [] 
            ],
        ];
    }

    private function transformProjectData(Project $project): ?array
    {
        try {
            $start = Carbon::parse($project->start_date);
            $end = Carbon::parse($project->deadline);
            
            // Normalisasi status ke string (antisipasi jika pakai Enum)
            $status = $project->status->value ?? $project->status ?? 'pending';
            
            // Format teks status untuk tampilan (misal: in_progress -> In Progress)
            $statusLabel = ucwords(str_replace('_', ' ', $status));

            return [
                'id' => $project->id,
                'text' => $project->title ?? 'Untitled',
                'start_date' => $start->format('Y-m-d'), 
                'duration' => max(1, $start->diffInDays($end)),
                'progress' => ($project->progress ?? 0) / 100,
                'status' => $statusLabel,
                
                // WARNA: Murni mengikuti status database
                'color' => $this->getStatusColor($status),
                'textColor' => '#ffffff'
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Logika Warna Strict (Hanya berdasarkan Status)
     */
    private function getStatusColor(string $status): string
    {
        return match (strtolower($status)) {
            'completed', 'done', 'finished' => '#10b981', // Green
            'pending', 'draft'              => '#9ca3af', // Gray
            'cancelled'                     => '#ef4444', // Red
            'in_progress'                   => '#3b82f6', // Blue (Tetap Biru walau telat)
            default                         => '#3b82f6', // Default Blue
        };
    }
}