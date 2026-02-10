<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Filament\Resources\ProjectRequests\ProjectRequestResource;
use App\Filament\Resources\ProjectRequests\Schemas\ProjectRequestForm;
use App\Models\ProjectRequest;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
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
            ]);
    }
}
