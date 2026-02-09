<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\ProjectStatus;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class MembersRelationManager extends RelationManager
{
    protected static string $relationship = 'members';

    protected static ?string $title = 'Team Members';

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        return (string) ($ownerRecord->members_count ?? $ownerRecord->members()->count());
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Add Member')
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['name', 'email'])
                    ->color('primary')
                    ->recordSelectOptionsQuery(function ($query) {
                        return $query->whereHas('roles', function ($q) {
                            $q->where('name', 'developer');
                        });
                    })
                    ->after(function () {
                        $this->updateProjectStatusIfPending();
                    }),
            ])
            ->recordActions([
                DetachAction::make()
                    ->label('Remove'),
            ])
            ->emptyStateHeading('No team members')
            ->emptyStateDescription('Add team members to this project.')
            ->emptyStateActions([
                AttachAction::make()
                    ->label('Add Member')
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['name', 'email'])
                    ->recordSelectOptionsQuery(function ($query) {
                        return $query->whereHas('roles', function ($q) {
                            $q->where('name', 'developer');
                        });
                    })
                    ->after(function () {
                        $this->updateProjectStatusIfPending();
                    }),
            ]);
    }

    private function updateProjectStatusIfPending(): void
    {
        $project = $this->ownerRecord;
        if ($project?->status === ProjectStatus::Pending) {
            $project->updateQuietly(['status' => ProjectStatus::InProgress]);
            $this->ownerRecord->refresh();
            $this->dispatch('project-status-updated');
        }
    }
}
