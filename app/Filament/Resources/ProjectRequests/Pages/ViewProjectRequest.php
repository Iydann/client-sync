<?php

namespace App\Filament\Resources\ProjectRequests\Pages;

use App\Filament\Resources\ProjectRequests\ProjectRequestResource;
use App\Models\Project;
use App\Models\ProjectRequest;
use App\ProjectRequestStatus;
use App\ProjectRequestType;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ViewProjectRequest extends ViewRecord
{
    protected static string $resource = ProjectRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createProject')
                ->label('Create Project')
                ->visible(function (ProjectRequest $record): bool {
                    return $record->type === ProjectRequestType::NewProject
                        && !$record->project_id
                        && (Auth::user()?->can('Create:Project') ?? false);
                })
                ->form([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->default(fn (ProjectRequest $record) => $record->title),
                    Textarea::make('description')
                        ->rows(5)
                        ->default(fn (ProjectRequest $record) => $record->description),
                ])
                ->action(function (ProjectRequest $record, array $data): void {
                    if (!$record->client_id) {
                        throw ValidationException::withMessages([
                            'client_id' => 'Client is required to create a project from this request.',
                        ]);
                    }

                    $project = Project::create([
                        'client_id' => $record->client_id,
                        'title' => $data['title'],
                        'description' => $data['description'],
                        'contract_value' => 0,
                        'status' => 'pending',
                        'contract_date' => now()->toDateString(),
                    ]);

                    $record->update([
                        'project_id' => $project->id,
                        'status' => ProjectRequestStatus::Completed->value,
                        'last_message_at' => now(),
                    ]);

                    Notification::make()
                        ->title('Project created')
                        ->body('Project was created and linked to this request.')
                        ->success()
                        ->send();
                }),
            EditAction::make()
                ->visible(fn () => ProjectRequestResource::isAdminUser()),
            DeleteAction::make()
                ->visible(fn () => ProjectRequestResource::isAdminUser()),
        ];
    }
}
