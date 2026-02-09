<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Attributes\On;
use App\Filament\Traits\HasGlobalYearFilter; 
use Illuminate\Support\Facades\Auth;

class UserContributions extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'User Contributions';
    protected static string|\UnitEnum|null $navigationGroup = 'Analytics';
    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.pages.user-contributions';

    public static function canAccess(): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        return !$user->hasRole('client') && $user->can('View:UserContributions');
    }

    use HasGlobalYearFilter;

    protected function getViewData(): array
    {
        $year = session('project_year', now()->year);

        if ($year === 'all') {
            $from = now()->subYear()->startOfDay(); 
            $to   = now()->endOfDay();
        } else {
            $yearInt = (int) $year;
            $from = Carbon::createFromDate($yearInt, 1, 1)->startOfDay();
            $to   = Carbon::createFromDate($yearInt, 12, 31)->endOfDay();
        }
        
        $users = User::role('developer')->get(); 

        $stats = [];

        foreach ($users as $user) {
            $contributions = $user->contributions() 
                ->whereBetween('created_at', [$from, $to])
                ->get()
                ->groupBy(fn ($c) => $c->created_at->toDateString());

            $period = new \DatePeriod(
                $from,
                new \DateInterval('P1D'),
                $to->copy()->addDay()
            );

            $heatmap = [];

            foreach ($period as $date) {
                $key = $date->format('Y-m-d');
                $count = isset($contributions[$key]) ? $contributions[$key]->count() : 0;

                $heatmap[] = [
                    'date' => $key,
                    'count' => $count,
                    'level' => match (true) {
                        $count === 0 => 0,
                        $count <= 2 => 1,
                        $count <= 4 => 2,
                        $count <= 6 => 3,
                        default     => 4,
                    },
                ];
            }

            $stats[$user->id] = [
                'total' => $contributions->flatten()->count(),
                'heatmap' => $heatmap,
            ];
        }
        return compact('users', 'stats', 'from', 'year');
    }
}