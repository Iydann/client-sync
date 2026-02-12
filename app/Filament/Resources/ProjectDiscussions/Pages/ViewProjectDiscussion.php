<?php

namespace App\Filament\Resources\ProjectDiscussions\Pages;

use App\Filament\Resources\ProjectDiscussions\ProjectDiscussionResource;
use App\Models\Project;
use App\Models\ProjectDiscussion;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Hidden;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;

class ViewProjectDiscussion extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = ProjectDiscussionResource::class;

    protected string $view = 'filament.resources.project-discussions.pages.view-project-discussion';

    protected static bool $shouldRegisterNavigation = false;

    public ?Project $project = null;

    public function mount(int | string $projectId): void
    {
        // Eager load relationships for better performance
        $this->project = Project::query()
            ->with(['client', 'discussions'])
            ->findOrFail($projectId);
    }

    public function getTitle(): string
    {
        return $this->project->title . ' - Discussions';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make('new_message')
                ->label('New Message')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->model(ProjectDiscussion::class)
                ->form([
                    Hidden::make('project_id')
                        ->default($this->project->id),
                    Hidden::make('user_id')
                        ->default(Auth::id()),
                    Textarea::make('message')
                        ->required()
                        ->rows(4)
                        ->placeholder('Type your message here...')
                        ->columnSpanFull(),
                ])
                ->mutateFormDataUsing(function (array $data): array {
                    $data['project_id'] = $this->project->id;
                    $data['user_id'] = Auth::id();
                    return $data;
                })
                ->after(fn () => $this->redirect(ProjectDiscussionResource::getUrl('view', ['projectId' => $this->project->id]))),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(ProjectDiscussion::query()->where('project_id', $this->project->id))
            ->columns([
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('message')
                    ->label('Message')
                    ->searchable()
                    ->limit(100)
                    ->wrap(),
                TextColumn::make('created_at')
                    ->label('Posted At')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
    }

    protected function getViewData(): array
    {
        return [
            'project' => $this->project,
        ];
    }

    public function getHeading(): string
    {
        return $this->project->title;
    }

    public function getSubheading(): ?string
    {
        return 'Project Discussions';
    }

    public function getBreadcrumbs(): array
    {
        return [
            ProjectDiscussionResource::getUrl('index') => 'Discussions',
            '#' => $this->project->title,
        ];
    }
}
