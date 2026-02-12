<?php

namespace App\Filament\Pages;

use App\Models\Project;
use Carbon\Carbon;
use Filament\Pages\Page;
use Livewire\Attributes\On;
use App\Filament\Traits\HasGlobalYearFilter;
use Illuminate\Support\Facades\Auth;

class ProjectTimeline extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $title = 'Project Timeline';
    protected string $view = 'filament.pages.project-timeline';

    use HasGlobalYearFilter;

    public static function canAccess(): bool
    {
        return Auth::user()?->can('View:ProjectTimeline') ?? false;
    }

    public function getViewData(): array
    {
        $year = session('project_year', now()->year);
        $user = Auth::user();

        // Base Query
        $query = Project::query()
            ->whereNotNull('start_date')
            ->whereNotNull('deadline');

        // Filter Client-Specific (KEAMANAN DATA)
        if ($user && $user->hasRole('client')) {
            $clientId = $user->client?->id;
            
            if ($clientId) {
                $query->where('client_id', $clientId);
            } else {
                // Jika user client tapi tidak punya data client, kosongkan hasil (safety)
                $query->whereNull('id'); 
            }
        }

        if ($user && $user->hasRole('developer')) {
            $query->whereHas('members', function ($subQuery) use ($user) {
                $subQuery->where('users.id', $user->id);
            });
        }

        // Filter Tahun (LOGIKA BARU: OVERLAP / IRISAN)
        // Menangani kasus proyek lintas tahun (misal: Mulai 2025, Selesai 2026)
        // agar muncul di filter 2025 MAUPUN 2026.
        if ($year !== 'all') {
            // Konversi tahun ke tanggal awal & akhir
            $startOfYear = Carbon::createFromDate($year, 1, 1)->startOfDay();
            $endOfYear   = Carbon::createFromDate($year, 12, 31)->endOfDay();

            // Ambil project yang memenuhi SALAH SATU syarat:
            $query->where(function ($q) use ($startOfYear, $endOfYear) {
                $q->whereBetween('start_date', [$startOfYear, $endOfYear])
                  ->orWhereBetween('deadline', [$startOfYear, $endOfYear]) 
                  ->orWhere(function ($sub) use ($startOfYear, $endOfYear) {
                      // Mulai SEBELUM tahun ini DAN Selesai SETELAH tahun ini (Melintasi penuh)
                      $sub->where('start_date', '<', $startOfYear)
                          ->where('deadline', '>', $endOfYear);
                  });
            });
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
            $deadline = Carbon::parse($project->deadline); 

            // Validasi: Deadline tidak boleh sebelum Start Date
            if ($deadline->lt($start)) {
                $deadline = $start->copy();
            }
            
            // Logika Inclusive End Date (+1 hari agar terarsir di Gantt)
            $ganttEndDate = $deadline->copy()->addDay();
            $duration = $start->diffInDays($ganttEndDate);

            $status = $project->status instanceof \UnitEnum 
                ? $project->status->value 
                : ($project->status ?? 'pending');
                
            $statusLabel = ucwords(str_replace('_', ' ', $status));

            return [
                'id' => $project->id,
                'text' => $project->title ?? 'Untitled',
                
                'start_date' => $start->format('Y-m-d'),
                'end_date' => $ganttEndDate->format('Y-m-d'), // Tanggal akhir untuk Gantt Chart (+1 hari)
                'deadline' => $deadline->format('Y-m-d'),     // Tanggal asli untuk Tooltip
                
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