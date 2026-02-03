<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth; // Tambahan Import

class UpcomingProjectDeadlines extends BaseWidget
{
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn (Project $record) => route('filament.admin.resources.projects.view', $record))
            ->heading('Upcoming Project Deadlines (Next 30 Days)')
            ->query(
                Project::query()
                    ->whereNotNull('deadline')
                    ->whereBetween('deadline', [
                        now()->startOfDay(),
                        now()->addDays(30)->endOfDay()
                    ])
                    ->where('status', '!=', 'completed')
                    ->when(Auth::user()?->hasRole('client'), function ($q) {
                        $q->where('client_id', Auth::user()->client?->id);
                    })
                    ->orderBy('deadline', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Project Title')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('client.client_name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('progress')
                    ->label('Progress')
                    ->formatStateUsing(fn ($state) => $state . '%')
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 80 => 'success',
                        $state >= 50 => 'warning',
                        default => 'danger',
                    }),
                
                // payment progress
                Tables\Columns\TextColumn::make('payment_progress')
                    ->label('Payment Progress')
                    ->formatStateUsing(fn ($state) => $state . '%')
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 80 => 'success',
                        $state >= 50 => 'warning',
                        default => 'danger',
                    }),

                Tables\Columns\TextColumn::make('deadline')
                    ->label('Deadline')
                    ->date('d M Y')
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => $this->getDeadlineColor($record)),
                
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable(),
            ])
            ->paginated([5, 10]);
    }

    protected function getDeadlineColor($record): string
    {
        $daysUntilDeadline = now()->diffInDays($record->deadline, false);
        
        return match (true) {
            $daysUntilDeadline < 0 => 'danger',  // Overdue
            $daysUntilDeadline <= 7 => 'danger', // Less than a week
            $daysUntilDeadline <= 14 => 'warning', // Less than 2 weeks
            default => 'success',
        };
    }
}