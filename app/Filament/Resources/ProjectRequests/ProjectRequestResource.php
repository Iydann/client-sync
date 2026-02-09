<?php

namespace App\Filament\Resources\ProjectRequests;

use App\Filament\Resources\ProjectRequests\Pages\CreateProjectRequest;
use App\Filament\Resources\ProjectRequests\Pages\EditProjectRequest;
use App\Filament\Resources\ProjectRequests\Pages\ListProjectRequests;
use App\Filament\Resources\ProjectRequests\Pages\ViewProjectRequest;
use App\Filament\Resources\ProjectRequests\Schemas\ProjectRequestForm;
use App\Filament\Schemas\Components\ProjectRequestInfolist;
use App\Models\ProjectRequest;
use App\Models\User;
use App\ProjectRequestType;
use App\Providers\Filament\AdminPanelProvider;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ProjectRequestResource extends Resource
{
    protected static ?string $model = ProjectRequest::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Request';

    protected static ?string $modelLabel = 'Request';

    protected static ?string $pluralModelLabel = 'Request';

    protected static ?int $navigationSort = 4;

    protected static string|\UnitEnum|null $navigationGroup = 'Project Management';

    public static function getNavigationGroup(): string
    {
        return AdminPanelProvider::getNavigationGroupName();
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            return $query;
        }

        if (self::isClientUser($user)) {
            return $query->where('client_id', $user->client?->id);
        }

        if (self::isDeveloperUser($user)) {
            $query->whereHas('project.members', fn (Builder $q) => $q->where('users.id', $user->id));
        }

        $year = session('project_year', now()->year);

        if ($year && $year !== 'all') {
            $query->whereYear('created_at', $year);
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        $typeField = ProjectRequestForm::getTypeField()
            ->live()
            ->afterStateUpdated(function (?string $state, callable $set): void {
                if ($state === ProjectRequestType::NewProject->value) {
                    $set('project_id', null);
                }
            });

        return $schema->components([
            $typeField,
            Hidden::make('client_id')
                ->default(fn () => Auth::user()?->client?->id)
                ->dehydrated(fn (callable $get) => $get('type') === ProjectRequestType::NewProject->value)
                ->visible(fn () => self::isClientUser()),
            Select::make('client_id')
                ->label('Client')
                ->relationship('client', 'client_name')
                ->searchable()
                ->preload()
                ->visible(fn (callable $get) => $get('type') === ProjectRequestType::NewProject->value && self::isAdminUser())
                ->required(fn (callable $get) => $get('type') === ProjectRequestType::NewProject->value && self::isAdminUser())
                ->dehydrated(fn (callable $get) => $get('type') === ProjectRequestType::NewProject->value && self::isAdminUser()),
            Select::make('project_id')
                ->label('Project')
                ->relationship('project', 'title', function (Builder $query) {
                    /** @var User|null $user */
                    $user = Auth::user();

                    if (!$user) {
                        return $query;
                    }

                    if (self::isClientUser($user)) {
                        return $query->where('client_id', $user->client?->id);
                    }

                    if (self::isDeveloperUser($user)) {
                        return $query->whereHas('members', fn (Builder $q) => $q->where('users.id', $user->id));
                    }

                    return $query;
                })
                ->searchable()
                ->preload()
                ->required(fn (callable $get) => $get('type') !== ProjectRequestType::NewProject->value)
                ->hidden(fn (callable $get) => $get('type') === ProjectRequestType::NewProject->value)
                ->disabled(fn (callable $get) => $get('type') === ProjectRequestType::NewProject->value)
                ->dehydrated(fn (callable $get) => $get('type') !== ProjectRequestType::NewProject->value)
                ->disabled(fn (string $context) => $context === 'edit'),
            ...ProjectRequestForm::getBaseFields(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ...ProjectRequestForm::getTableColumns(showProject: true),
                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(ProjectRequestForm::enumOptions(\App\ProjectRequestStatus::class)),
                SelectFilter::make('type')
                    ->options(ProjectRequestForm::enumOptions(\App\ProjectRequestType::class)),
            ])
            ->defaultSort('last_message_at', 'desc')
            ->recordUrl(fn (ProjectRequest $record) => self::getUrl('view', ['record' => $record]))
            ->actions([
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make()
                    ->visible(fn () => self::isAdminUser()),
                \Filament\Actions\DeleteAction::make()
                    ->visible(fn () => self::isAdminUser()),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make()
                        ->visible(fn () => self::isAdminUser()),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProjectRequestInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjectRequests::route('/'),
            'create' => CreateProjectRequest::route('/create'),
            'view' => ViewProjectRequest::route('/{record}'),
            'edit' => EditProjectRequest::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return self::isAdminUser() || self::isClientUser();
    }

    public static function canEdit($record): bool
    {
        return self::isAdminUser();
    }

    public static function canDelete($record): bool
    {
        return self::isAdminUser();
    }

    // ── Role helpers (used across resource + relation manager) ──

    public static function isAdminUser(?User $user = null): bool
    {
        $user ??= Auth::user();

        if (!$user) {
            return false;
        }

        return !$user->hasRole('client') && !$user->hasRole('developer');
    }

    public static function isClientUser(?User $user = null): bool
    {
        $user ??= Auth::user();
        return $user?->hasRole('client') ?? false;
    }

    public static function isDeveloperUser(?User $user = null): bool
    {
        $user ??= Auth::user();
        return $user?->hasRole('developer') ?? false;
    }
}
