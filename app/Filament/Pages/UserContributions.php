<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\User;
use Illuminate\Support\Carbon;

class UserContributions extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'User Contributions';
    protected static string|\UnitEnum|null $navigationGroup = 'Analytics';
    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.pages.user-contributions';

    protected function getViewData(): array
    {
        $from = now()->subMonths(3)->startOfDay();
        $to   = now()->endOfDay();

        $users = User::query()->get();

        $stats = [];

        foreach ($users as $user) {
            // contoh kontribusi → ganti sesuai model aslimu
            $contributions = $user->contributions()
                ->whereBetween('created_at', [$from, $to])
                ->get()
                ->groupBy(fn ($c) => $c->created_at->toDateString());

            $period = new \DatePeriod(
                $from,
                new \DateInterval('P1D'),
                $to
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

        return compact('users', 'stats', 'from');
    }
}
