<?php

namespace App\Filament\Resources\Milestones;

use App\Filament\Resources\Milestones\Pages\CreateMilestone;
use App\Filament\Resources\Milestones\Pages\EditMilestone;
use App\Filament\Resources\Milestones\Pages\ListMilestones;
use App\Filament\Resources\Milestones\Schemas\MilestoneForm;
use App\Filament\Resources\Milestones\Tables\MilestonesTable;
use App\Models\Milestone;
use App\Models\User;
use BackedEnum;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MilestoneResource extends Resource
{
    protected static ?string $model = Milestone::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static bool $shouldRegisterNavigation = false;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        /** @var User|null $user */
        $user = Auth::user();
        
        // If user is a client, only show milestones for their projects
        if ($user && $user->hasRole('client')) {
            $query->whereHas('project', function ($q) use ($user) {
                $q->where('client_id', $user->client?->id);
            });
        }
        
        return $query;
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMilestones::route('/'),
            'create' => CreateMilestone::route('/create'),
            'edit' => EditMilestone::route('/{record}/edit'),
        ];
    }
}
