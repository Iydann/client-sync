<?php

namespace App\Filament\Pages;

use App\Models\Client;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Illuminate\Database\Eloquent\Model;
use Filament\Support\Enums\FontWeight;

class ClientInsights extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?string $navigationLabel = 'Client Insights'; // Nama di Menu
    protected static ?string $title = 'Client Insights Overview'; // Judul Halaman
    protected static string|\UnitEnum|null $navigationGroup = 'Analytics';
    protected static ?int $navigationSort = 2;

    // View ini perlu kita buat di langkah ke-3 (tapi biasanya auto-generated)
    protected string $view = 'filament.pages.client-insights';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Eager loading sangat PENTING disini untuk menghindari N+1 Query
                // karena kita akan menghitung invoice dari dalam project
                Client::query()->with(['projects.invoices'])
            )
            ->columns([
                // 1. Nama Client
                TextColumn::make('client_name')
                    ->label('Client Name')
                    ->searchable()
                    ->weight(FontWeight::Bold)
                    ->description(fn (Client $record) => $record->client_type->name ?? '-'),

                // 2. Jumlah Proyek (Total & Aktif)
                TextColumn::make('projects_count')
                    ->label('Projects')
                    ->counts('projects') // Menghitung total project
                    ->formatStateUsing(function (string $state, Client $record) {
                        // Custom format: "Total (Active)"
                        $active = $record->projects->where('status.value', 'in_progress')->count();
                        // Fallback jika status bukan enum, sesuaikan dengan string 'in_progress'
                        if ($active === 0) {
                            $active = $record->projects->where('status', 'in_progress')->count();
                        }
                        return "{$state} Total ({$active} Active)";
                    })
                    ->color('gray'),

                // 3. Total Contract Value
                TextColumn::make('total_contract')
                    ->label('Contract Value')
                    ->state(function (Client $record) {
                        return $record->projects->sum('contract_value');
                    })
                    ->money('IDR', locale: 'id')
                    ->sortable(query: function ($query, string $direction) {
                        // Custom sort logic karena ini calculated column
                        return $query->withSum('projects', 'contract_value')
                                     ->orderBy('projects_sum_contract_value', $direction);
                    }),

                // 4. Total Paid (Revenue Real)
                TextColumn::make('total_paid')
                    ->label('Paid Revenue')
                    ->state(function (Client $record) {
                        // Masuk ke projects -> ambil invoices -> filter paid -> sum amount
                        return $record->projects->flatMap->invoices
                            ->where('status', 'paid')
                            ->sum('amount');
                    })
                    ->money('IDR', locale: 'id')
                    ->color('success'),

                // 5. Outstanding (Sisa Tagihan + Belum Ditagih)
                TextColumn::make('outstanding')
                    ->label('Unpaid & Uninvoiced')
                    ->state(function (Client $record) {
                        $contract = $record->projects->sum('contract_value');
                        $paid = $record->projects->flatMap->invoices
                            ->where('status', 'paid')
                            ->sum('amount');
                        
                        return max(0, $contract - $paid);
                    })
                    ->money('IDR', locale: 'id')
                    ->color('warning')
                    ->weight(FontWeight::Medium),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50]);
    }
}