<?php

namespace App\Filament\Resources\Clients\RelationManagers;

use App\Filament\Resources\Projects\Tables\ProjectsTable;
use App\ProjectStatus;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'projects';

    protected static ?string $title = 'Projects';

    public function table(Table $table): Table
    {
        return ProjectsTable::configure($table)
            ->columns([
                TextColumn::make('title')
                    ->label('Project Title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('progress')
                    ->label('Progress')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state . '%'),
                TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('contract_value')
                    ->label('Contract Value')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => 'IDR ' . number_format($state, 0, ',', '.')),
                TextColumn::make('payment_progress')
                    ->label('Payment Progress')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state . '%'),
                TextColumn::make('deadline')
                    ->date()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                CreateAction::make()
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('status')
                            ->options([
                                ProjectStatus::Pending->value => ProjectStatus::Pending->getLabel(),
                                ProjectStatus::InProgress->value => ProjectStatus::InProgress->getLabel(),
                                ProjectStatus::Completed->value => ProjectStatus::Completed->getLabel(),
                                ProjectStatus::Cancelled->value => ProjectStatus::Cancelled->getLabel(),
                            ])
                            ->default(ProjectStatus::Pending->value)
                            ->required(),
                        Forms\Components\DatePicker::make('deadline')
                            ->label('Deadline'),
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('status')
                            ->options([
                                ProjectStatus::Pending->value => ProjectStatus::Pending->getLabel(),
                                ProjectStatus::InProgress->value => ProjectStatus::InProgress->getLabel(),
                                ProjectStatus::Completed->value => ProjectStatus::Completed->getLabel(),
                                ProjectStatus::Cancelled->value => ProjectStatus::Cancelled->getLabel(),
                            ])
                            ->required(),
                        Forms\Components\DatePicker::make('deadline')
                            ->label('Deadline'),
                    ]),
                DeleteAction::make(),
            ])
            ->emptyStateHeading('No projects')
            ->emptyStateDescription('Create a project for this client.')
            ->emptyStateActions([
                Action::make('create_project_page')
                    ->label('Create Project')
                    ->icon('heroicon-o-plus')
                    ->url(fn () => route('filament.admin.resources.projects.create', ['client_id' => $this->getOwnerRecord()->id]))
                    ->button(),
            ]);
    }
}