<?php

namespace App\Filament\Resources\Milestones;

use App\Filament\Resources\Milestones\Pages\CreateMilestone;
use App\Filament\Resources\Milestones\Pages\EditMilestone;
use App\Filament\Resources\Milestones\Pages\ListMilestones;
use App\Filament\Resources\Milestones\Pages\ViewMilestone; // <--- WAJIB: Import Page View
use App\Filament\Resources\Milestones\RelationManagers\TasksRelationManager;
use App\Models\Milestone;
use Filament\Actions\EditAction; 
use Filament\Actions\ViewAction; // <--- Import Action Unified
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema; // Menggunakan Schema (Filament 4.4 / Unified)
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table; 

class MilestoneResource extends Resource
{
    protected static ?string $model = Milestone::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([ 
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Select::make('project_id')
                    ->relationship('project', 'title')
                    ->disabled()
                    ->dehydrated()
                    ->required(),

                TextInput::make('order')
                    ->numeric(),

                Toggle::make('is_completed')
                    ->label('Milestone Completed')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),

                TextColumn::make('project.title')
                    ->sortable()
                    ->label('Project'),

                IconColumn::make('is_completed')
                    ->boolean(),
            ])
            ->actions([
                ViewAction::make(), // Tombol View standar
                EditAction::make(),
            ]);
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
            'view' => ViewMilestone::route('/{record}'), // <--- WAJIB: Daftarkan Route View
            'edit' => EditMilestone::route('/{record}/edit'),
        ];
    }
}