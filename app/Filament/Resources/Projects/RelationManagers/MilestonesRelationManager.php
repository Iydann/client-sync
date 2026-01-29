<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Filament\Resources\Milestones\MilestoneResource;
use App\Models\Milestone;
use App\Models\UserContribution;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema; 
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MilestonesRelationManager extends RelationManager
{
    protected static string $relationship = 'milestones';

    protected static ?string $title = 'Milestones';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('order')
                    ->numeric()
                    ->disabled()
                    ->dehydrated()
                    ->default(fn () => $this->ownerRecord->milestones()->max('order') + 1),

                SpatieMediaLibraryFileUpload::make('attachments')
                    ->collection('milestone-attachments')
                    ->multiple()
                    ->previewable()
                    ->openable()
                    ->maxFiles(5)
                    ->label('Attachments'),

                Toggle::make('is_completed')
                    ->label('Completed')
                    ->default(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->reorderable('order')
            ->defaultSort('order', 'asc')
            ->recordUrl(fn (Milestone $record) => MilestoneResource::getUrl('view', ['record' => $record]))
            
            ->modifyQueryUsing(function (Builder $query) {
                return $query->withCount([
                    'tasks',
                    'tasks as completed_tasks_count' => function (Builder $query) {
                        $query->where('is_completed', true);
                    },
                ]);
            })
            
            ->columns([
                TextColumn::make('order')
                    ->label('#')
                    ->sortable()
                    ->width(40),

                TextColumn::make('name')
                    ->searchable()
                    ->weight('bold'),

                // KOLOM BARU: TOTAL TASK
                TextColumn::make('tasks_count')
                    ->label('Total Tasks')
                    ->badge()
                    ->color('gray')
                    ->alignCenter(),

                // KOLOM BARU: PROGRESS (%)
                TextColumn::make('progress')
                    ->label('Progress')
                    ->state(function (Milestone $record) {
                        // Menggunakan data yang sudah di-load oleh modifyQueryUsing di atas
                        $total = $record->tasks_count;
                        $done = $record->completed_tasks_count;
                        
                        if ($total == 0) return '0%';
                        
                        $percentage = round(($done / $total) * 100);
                        return "{$percentage}%";
                    })
                    ->badge()
                    ->color(fn (string $state) => match (true) {
                        $state === '100%' => 'success',
                        $state === '0%' => 'gray',
                        default => 'warning',
                    }),

                IconColumn::make('is_completed')
                    ->boolean()
                    ->label('Done'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->after(fn () => $this->logContribution('create_milestone')),
            ])
            ->actions([
                ViewAction::make()
                    ->url(fn (Milestone $record) => MilestoneResource::getUrl('view', ['record' => $record])),
                EditAction::make()
                    ->url(fn (Milestone $record) => MilestoneResource::getUrl('edit', ['record' => $record])),
                DeleteAction::make()
                    ->after(fn () => $this->logContribution('delete_milestone')),
            ]);
    }

    protected function logContribution(string $type)
    {
        $user = Auth::user();
        if ($user) {
            UserContribution::create([
                'user_id' => $user->id,
                'type' => $type,
                'value' => 1,
                'year' => now()->year,
            ]);
        }
    }
}