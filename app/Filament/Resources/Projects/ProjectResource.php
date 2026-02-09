<?php

namespace App\Filament\Resources\Projects;

use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Filament\Resources\Projects\Pages\ViewProject;
use App\Filament\Resources\Projects\Schemas\ProjectForm;
use App\Filament\Resources\Projects\Tables\ProjectsTable;
use App\Filament\Resources\Projects\RelationManagers;
use App\Filament\Schemas\Components\ProjectInfolist;
use App\Models\Project;
use App\Models\User;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Providers\Filament\AdminPanelProvider;
use Illuminate\Support\Facades\Auth;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static string|UnitEnum|null $navigationGroup = "Project Management";
    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getNavigationGroup(): string
    {
        return AdminPanelProvider::getNavigationGroupName();
    }

    /**
     * Memodifikasi Query Global untuk Resource ini
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            return $query;
        }
        
        if ($user->hasRole('client')) {
            $query->where('client_id', $user->client?->id);
        }

        if ($user->hasRole('developer')) {
            $query->whereHas('members', function (Builder $subQuery) use ($user) {
                $subQuery->where('users.id', $user->id);
            });
        }
    
        $year = session('project_year', now()->year);

        if ($year && $year !== 'all') {
            $query->whereYear('contract_date', $year);
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return ProjectForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProjectsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProjectInfolist::configure($schema);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\MilestonesRelationManager::class,
            RelationManagers\MembersRelationManager::class,
            RelationManagers\InvoicesRelationManager::class,
            RelationManagers\OtherProjectsRelationManager::class,
            RelationManagers\ProjectRequestsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjects::route('/'),
            'create' => CreateProject::route('/create'),
            'view' => ViewProject::route('/{record}'),
            'edit' => EditProject::route('/{record}/edit'),
        ];
    }
}