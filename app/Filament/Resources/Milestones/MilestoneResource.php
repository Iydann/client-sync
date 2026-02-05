<?php

namespace App\Filament\Resources\Milestones;

use App\Filament\Resources\Milestones\Pages\CreateMilestone;
use App\Filament\Resources\Milestones\Pages\EditMilestone;
use App\Filament\Resources\Milestones\Pages\ListMilestones;
use App\Filament\Resources\Milestones\Pages\ViewMilestone;
use App\Filament\Resources\Milestones\RelationManagers\TasksRelationManager;
use App\Filament\Resources\Milestones\Schemas\MilestoneForm;
use App\Filament\Resources\Milestones\Tables\MilestonesTable;
use App\Models\Milestone;
use Filament\Actions\EditAction; 
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table; 

class MilestoneResource extends Resource
{
    protected static ?string $model = Milestone::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function canAccess(): bool
    {
        // Allow access only if coming from a relation manager (has project_id in URL)
        $request = request();
        $milestonId = $request->route('record');
        
        if (!$milestonId) {
            return false;
        }

        $milestone = Milestone::find($milestonId);
        if (!$milestone) {
            return false;
        }

        // Check if user can view the associated project
        return auth()->user()?->can('view', $milestone->project) ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return MilestoneForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MilestonesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            TasksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMilestones::route('/'),
            'create' => CreateMilestone::route('/create'),
            'view' => ViewMilestone::route('/{record}'),
            'edit' => EditMilestone::route('/{record}/edit'),
        ];
    }
}