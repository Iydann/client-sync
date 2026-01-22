<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Filament\Resources\Milestones\Tables\MilestonesTable;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Illuminate\Support\Facades\Auth;
use App\Models\UserContribution;

class MilestonesRelationManager extends RelationManager
{
    protected static string $relationship = 'milestones';

    protected static ?string $title = 'Milestones';

    protected function getCreateFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('order')
                ->numeric()
                ->disabled()
                ->dehydrated()
                ->default(function () {
                    $maxOrder = $this->ownerRecord->milestones()->max('order') ?? -1;
                    return $maxOrder + 1;
                })
                ->helperText('Automatically set to next sequence'),
            SpatieMediaLibraryFileUpload::make('attachments')
                ->collection('milestone-attachments')
                ->previewable()
                ->openable()
                ->multiple()
                ->maxFiles(5)
                ->label('Attachments')
                ->helperText('Upload documents, images or files related to this milestone')
                ->live(),
            Forms\Components\Toggle::make('is_completed')
                ->label('Completed')
                ->default(false)
                ->disabled(fn ($get) => empty($get('attachments')))
                ->helperText(fn ($get) => empty($get('attachments')) ? 'Upload at least one file to mark as completed' : 'Mark this milestone as completed'),
        ];
    }

    protected function getEditFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('order')
                ->numeric()
                ->helperText('Change order to resequence milestone'),
            SpatieMediaLibraryFileUpload::make('attachments')
                ->collection('milestone-attachments')
                ->multiple()
                ->previewable()
                ->openable()
                ->maxFiles(5)
                ->label('Attachments')
                ->helperText('Upload documents, images or files related to this milestone')
                ->live(),
            Forms\Components\Toggle::make('is_completed')
                ->label('Completed')
                ->disabled(function ($get, $record) {
                    $hasAttachments = $get('attachments') && count($get('attachments')) > 0;
                    $hasExistingMedia = $record && $record->getMedia('milestone-attachments')->count() > 0;
                    return !$hasAttachments && !$hasExistingMedia;
                })
                ->helperText(function ($get, $record) {
                    $hasAttachments = $get('attachments') && count($get('attachments')) > 0;
                    $hasExistingMedia = $record && $record->getMedia('milestone-attachments')->count() > 0;
                    if (!$hasAttachments && !$hasExistingMedia) {
                        return 'Upload at least one file to mark as completed';
                    }
                    return 'Mark this milestone as completed';
                }),
        ];
    }

    public function table(Table $table): Table
    {
        return MilestonesTable::configure($table)
            ->reorderable('order')
            ->defaultSort('order')
            ->headerActions([
                CreateAction::make()
                    ->schema(fn () => $this->getCreateFormSchema())
                    ->after(function ($record) {
                        $user = \Illuminate\Support\Facades\Auth::user();
                        if ($user) {
                            \App\Models\UserContribution::create([
                                'user_id' => $user->id,
                                'type' => 'create_milestone',
                                'value' => 1,
                                'year' => now()->year,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->schema(fn () => $this->getEditFormSchema())
                    ->after(function ($record) {
                        $user = \Illuminate\Support\Facades\Auth::user();
                        if ($user) {
                            \App\Models\UserContribution::create([
                                'user_id' => $user->id,
                                'type' => 'update_milestone',
                                'value' => 1,
                                'year' => now()->year,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }),
                DeleteAction::make()
                    ->after(function ($record) {
                        $user = \Illuminate\Support\Facades\Auth::user();
                        if ($user) {
                            \App\Models\UserContribution::create([
                                'user_id' => $user->id,
                                'type' => 'delete_milestone',
                                'value' => 1,
                                'year' => now()->year,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }),
            ])
            ->emptyStateHeading('No milestones')
            ->emptyStateDescription('Add milestones to track project progress.')
            ->emptyStateActions([
                CreateAction::make()
                    ->schema(fn () => $this->getCreateFormSchema())
                    ->after(function ($record) {
                        $user = \Illuminate\Support\Facades\Auth::user();
                        if ($user) {
                            \App\Models\UserContribution::create([
                                'user_id' => $user->id,
                                'type' => 'create_milestone',
                                'value' => 1,
                                'year' => now()->year,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }),
            ]);
    }

    public function afterCreate($record): void
    {
        $user = Auth::user();
        if ($user) {
            UserContribution::create([
                'user_id' => $user->id,
                'type' => 'create_milestone',
                'value' => 1,
                'year' => now()->year,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function afterEdit($record): void
    {
        $user = Auth::user();
        if ($user) {
            UserContribution::create([
                'user_id' => $user->id,
                'type' => 'update_milestone',
                'value' => 1,
                'year' => now()->year,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function afterDelete($record): void
    {
        $user = Auth::user();
        if ($user) {
            UserContribution::create([
                'user_id' => $user->id,
                'type' => 'delete_milestone',
                'value' => 1,
                'year' => now()->year,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}