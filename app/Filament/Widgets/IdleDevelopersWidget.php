<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class IdleDevelopersWidget extends TableWidget
{
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 'full';
    
    protected static ?string $heading = 'Idle Developers';

    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'admin']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                $this->getIdleDevelopersQuery()
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Developer Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('email')
                    ->icon('heroicon-m-envelope')
                    ->copyable(),

                Tables\Columns\TextColumn::make('last_project')
                    ->label('Last Project')
                    ->getStateUsing(fn (User $record): string => $this->formatLastProject($record))
                    ->sortable(),
            ])
            ->actions([
                // Aksi diarahkan ke Edit User agar bisa menambah Project di sana
                Action::make('assign_project')
                    ->label('Assign Project')
                    ->icon('heroicon-m-briefcase')
                    ->url(fn (): string => $this->getAssignProjectUrl())
                    ->button(),
            ])
            ->paginated(false);
    }

    private function getIdleDevelopersQuery(): Builder
    {
        return User::query()
            ->whereHas('roles', fn (Builder $query) => $query->where('name', 'developer'))
            ->whereDoesntHave('projects', function (Builder $query) {
                $query->where('status', '!=', 'completed');
            })
            ->with(['projects' => function ($query) {
                $query->orderBy('project_members.created_at', 'desc');
            }]);
    }

    private function formatLastProject(User $record): string
    {
        $project = $record->projects->first();

        if (!$project) {
            return 'No Project Assigned';
        }

        $assignedAt = $project->pivot?->created_at?->format('d M Y');
        return $assignedAt
            ? "{$project->title} ({$assignedAt})"
            : $project->title;
    }

    private function getAssignProjectUrl(): string
    {
        return rtrim((string) config('app.url'), '/') . '/portal/projects?tab=active';
    }
}