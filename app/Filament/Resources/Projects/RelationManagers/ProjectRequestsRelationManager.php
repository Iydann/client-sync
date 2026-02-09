<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Filament\Resources\ProjectRequests\ProjectRequestResource;
use App\Filament\Resources\ProjectRequests\Schemas\ProjectRequestForm;
use App\Models\ProjectRequest;
use App\Models\ProjectRequestMessage;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ProjectRequestsRelationManager extends RelationManager
{
    protected static string $relationship = 'requests';

    protected static ?string $title = 'Request';

    protected static ?string $label = 'Request';

    protected static ?string $pluralLabel = 'Request';

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        return (string) ($ownerRecord->requests_count ?? $ownerRecord->requests()->count());
    }

    public function table(Table $table): Table
    {
        $formSchema = [
            ProjectRequestForm::getTypeField(),
            ...ProjectRequestForm::getBaseFields(),
        ];

        return $table
            ->recordTitleAttribute('title')
            ->columns(ProjectRequestForm::getTableColumns(showProject: false))
            ->defaultSort('last_message_at', 'desc')
            ->headerActions([
                CreateAction::make()
                    ->visible(fn () => ProjectRequestResource::isAdminUser() || ProjectRequestResource::isClientUser())
                    ->schema($formSchema)
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['created_by'] = Auth::id();
                        $data['client_id'] = $this->getOwnerRecord()->client_id;
                        $data['last_message_at'] = now();
                        return $data;
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn (ProjectRequest $record) => ProjectRequestResource::getUrl('view', ['record' => $record])),
                EditAction::make()
                    ->visible(fn () => ProjectRequestResource::isAdminUser())
                    ->schema($formSchema),
                DeleteAction::make()
                    ->visible(fn () => ProjectRequestResource::isAdminUser()),
                Action::make('reply')
                    ->label('Reply')
                    ->visible(fn (ProjectRequest $record) => $this->canReply($record))
                    ->form([
                        Textarea::make('message')
                            ->required()
                            ->rows(4),
                    ])
                    ->action(function (ProjectRequest $record, array $data): void {
                        ProjectRequestMessage::create([
                            'project_request_id' => $record->id,
                            'user_id' => Auth::id(),
                            'message' => $data['message'],
                        ]);
                    }),
            ]);
    }

    protected function canReply(ProjectRequest $request): bool
    {
        $user = Auth::user();

        if (!$user || !$user->can('Create:ProjectRequestMessage')) {
            return false;
        }

        if (ProjectRequestResource::isAdminUser($user)) {
            return true;
        }

        if (!$request->isDiscussionOpenForParticipants()) {
            return false;
        }

        if (ProjectRequestResource::isClientUser($user)) {
            return $request->client_id === $user->client?->id;
        }

        if (ProjectRequestResource::isDeveloperUser($user)) {
            return $request->project
                ? $request->project->members()->where('users.id', $user->id)->exists()
                : false;
        }

        return false;
    }
}
