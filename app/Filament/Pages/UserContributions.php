<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Attributes\On;

class UserContributions extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'User Contributions';
    protected static string|\UnitEnum|null $navigationGroup = 'Analytics';
    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.pages.user-contributions';

    public $year;

    public function mount()
    {
        $this->year = request()->integer('year', now()->year);
    }

    #[On('yearChanged')]
    public function updateYear($year)
    {
        $this->year = $year;
        // Livewire akan otomatis me-render ulang view karena properti berubah
    }

    protected function getViewData(): array
    {
        // Logika penentuan tanggal berdasarkan filter tahun
        if ($this->year === 'all') {
            $from = now()->subYear()->startOfDay(); // Default view jika All, misal 1 tahun terakhir
            $to   = now()->endOfDay();
        } else {
            $from = Carbon::createFromDate($this->year, 1, 1)->startOfDay();
            $to   = Carbon::createFromDate($this->year, 12, 31)->endOfDay();
        }

        $users = User::query()->get();
        $stats = [];

        foreach ($users as $user) {
            $contributions = $user->contributions() // Pastikan relasi ini ada di Model User
                ->whereBetween('created_at', [$from, $to])
                ->get()
                ->groupBy(fn ($c) => $c->created_at->toDateString());

            // Gunakan DatePeriod agar heatmap tetap terisi tanggalnya meski kosong datanya
            $period = new \DatePeriod(
                $from,
                new \DateInterval('P1D'),
                $to->copy()->addDay() // Tambah 1 hari agar tanggal terakhir inclusive
            );

            $heatmap = [];

            foreach ($period as $date) {
                $key = $date->format('Y-m-d');
                $count = isset($contributions[$key]) ? $contributions[$key]->count() : 0;

                // Batasi jumlah hari jika 'all' agar UI tidak meledak, 
                // atau biarkan jika library heatmap Anda bisa handle scroll.
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