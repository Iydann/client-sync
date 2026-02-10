<?php

namespace App\Filament\Resources\Projects\Tables;

use App\Models\Project;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client.client_name')
                    ->label('Client name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Project Title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('progress')
                    ->label('Progress')
                    ->sortable()
                    ->formatStateUsing(fn($state) => $state . '%'),
                TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('contract_value')
                    ->label('Contract Value')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => 'IDR ' . number_format($state, 0, ',', '.')),
                TextColumn::make('payment_progress')
                    ->label('Payment Progress')
                    ->sortable()
                    ->formatStateUsing(fn($state) => $state . '%'),
                TextColumn::make('deadline')
                    ->date()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn ($record) => route('filament.admin.resources.projects.view', ['record' => $record->id])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('relate_projects')
                        ->label('Relate Projects')
                        ->icon('heroicon-m-link')
                        ->modalHeading('Relate Projects')
                        ->modalDescription('Set selected projects as related to a parent project. All projects must belong to the same client.')
                        ->form([
                            Select::make('parent_project_id')
                                ->label('Parent project')
                                ->required()
                                ->searchable()
                                ->options(function () {
                                    return Project::query()
                                        ->with('client')
                                        ->orderBy('title')
                                        ->get()
                                        ->mapWithKeys(fn (Project $project) => [
                                            $project->id => ($project->client?->client_name ? $project->client->client_name . ' - ' : '') . $project->title,
                                        ])
                                        ->all();
                                }),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $parent = Project::with('parentProject')->find($data['parent_project_id']);

                            if (!$parent) {
                                Notification::make()
                                    ->title('Parent project not found')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            $root = $parent->parentProject ?? $parent;

                            $clientIds = $records->pluck('client_id')->filter()->unique();

                            if ($clientIds->count() !== 1 || $clientIds->first() !== $root->client_id) {
                                Notification::make()
                                    ->title('Projects must belong to the same client')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            $updated = 0;

                            foreach ($records as $record) {
                                if ($record->id === $root->id) {
                                    if ($record->parent_project_id !== null) {
                                        $record->update(['parent_project_id' => null]);
                                        $updated++;
                                    }
                                    continue;
                                }

                                if ($record->parent_project_id !== $root->id) {
                                    $record->update(['parent_project_id' => $root->id]);
                                    $updated++;
                                }
                            }

                            Notification::make()
                                ->title('Projects related')
                                ->body($updated . ' project(s) updated.')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
