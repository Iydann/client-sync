<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Filament\Resources\Projects\ProjectResource;
use App\Models\Project;
use App\ProjectStatus;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class OtherProjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'childProjects';

    protected static ?string $title = 'Related Projects';
    
    protected static ?string $label = 'Related Projects';
    
    protected static ?string $pluralLabel = 'Related Projects';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                // Get the current project
                $currentProject = $this->getOwnerRecord();
                
                // Find the root parent (original project)
                $rootProject = $currentProject->parentProject ?? $currentProject;
                
                // Build query to show all related projects
                // Clear the existing query and build a new one
                return Project::whereIn('id', function ($subquery) use ($rootProject) {
                    $subquery->select('id')
                        ->from('projects')
                        ->where('id', $rootProject->id)
                        ->orWhere('parent_project_id', $rootProject->id);
                })
                ->where('client_id', $currentProject->client_id)
                ->orderBy('parent_project_id')
                ->orderBy('created_at');
            })
            ->recordTitleAttribute('title')
            ->recordUrl(fn ($record) => $record->id === $this->getOwnerRecord()->id
                ? null
                : ProjectResource::getUrl('view', [
                    'record' => $record,
                    'relation' => $this->getRelationTabKey(),
                ]))
            ->columns([
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('contract_date')
                    ->label('Contract Date')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),
                    
                TextColumn::make('contract_value')
                    ->label('Contract Value')
                    ->money('IDR')
                    ->sortable(),
                    
                TextColumn::make('start_date')
                    ->label('Start Date')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),
                    
                TextColumn::make('deadline')
                    ->label('Deadline')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),
                    
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),
                    
                TextColumn::make('progress')
                    ->label('Progress')
                    ->suffix('%')
                    ->sortable(),
                    
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(ProjectStatus::class),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add Related Project')
                    ->icon('heroicon-o-plus')
                    ->modalHeading('Create Related Project')
                    ->modalDescription('Create a new project for the same client that is related to this project (e.g., Continuous 1, Maintenance, Feature Addition, etc.)')
                    ->successNotificationTitle('Related project created successfully')
                    ->using(function (array $data): Project {
                        // Ensure critical data is set
                        $data['client_id'] = $this->getOwnerRecord()->client_id;
                        $data['parent_project_id'] = $this->getOwnerRecord()->id;
                        
                        return Project::create($data);
                    })
                    ->form([
                        TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->maxLength(255)
                            ->default(fn () => $this->generateNextTitle($this->getOwnerRecord()))
                            ->placeholder('e.g., Continuous 1, Maintenance 2025, Feature Addition'),
                            
                        TextInput::make('contract_number')
                            ->label('Contract #')
                            ->maxLength(255)
                            ->default(fn () => $this->getOwnerRecord()->contract_number),
                            
                        DatePicker::make('contract_date')
                            ->label('Contract Date')
                            ->native(false),
                            
                        TextInput::make('contract_value')
                            ->label('Contract Value')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->required(),
                            
                        DatePicker::make('start_date')
                            ->label('Start Date')
                            ->native(false),
                            
                        DatePicker::make('deadline')
                            ->label('Deadline')
                            ->native(false),
                            
                        Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('e.g., Feature additions, maintenance work, bug fixes, system improvements...'),
                            
                        Select::make('status')
                            ->label('Status')
                            ->options(ProjectStatus::class)
                            ->default('pending')
                            ->required(),
                    ]),
            ])
            ->actions([
                ViewAction::make()
                    ->label(fn ($record) => $record->id === $this->getOwnerRecord()->id ? 'Viewed' : 'View')
                    ->color('gray')
                    ->disabled(fn ($record) => $record->id === $this->getOwnerRecord()->id)
                    ->url(fn ($record) => ProjectResource::getUrl('view', [
                        'record' => $record,
                        'relation' => $this->getRelationTabKey(),
                    ])),
                EditAction::make()
                    ->form([
                        TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->maxLength(255),
                            
                        TextInput::make('contract_number')
                            ->label('Contract #')
                            ->maxLength(255),
                            
                        DatePicker::make('contract_date')
                            ->label('Contract Date')
                            ->native(false),
                            
                        TextInput::make('contract_value')
                            ->label('Contract Value')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                            
                        DatePicker::make('start_date')
                            ->label('Start Date')
                            ->native(false),
                            
                        DatePicker::make('deadline')
                            ->label('Deadline')
                            ->native(false),
                            
                        Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->columnSpanFull(),
                            
                        Select::make('status')
                            ->label('Status')
                            ->options(ProjectStatus::class)
                            ->required(),
                    ]),
                DeleteAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('parent_project_id')
            ->emptyStateHeading('No related projects')
            ->emptyStateDescription('Create a related project to track continuous work, maintenance, or feature additions for the same client.')
            ->emptyStateIcon('heroicon-o-link');
    }

    /**
     * Generate the next project title based on existing related projects
     */
    private function generateNextTitle($parentProject): string
    {
        $baseTitle = $parentProject->title;
        
        // Count existing child projects
        $siblingCount = $parentProject->childProjects()->count();
        
        // Generate new title with increment
        return "{$baseTitle} - #" . ($siblingCount + 2);
    }

    private function getRelationTabKey(): string|int
    {
        $relations = ProjectResource::getRelations();

        foreach ($relations as $key => $relation) {
            if ($relation === static::class) {
                return $key;
            }
        }

        return static::class;
    }
}

