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

class MilestonesRelationManager extends RelationManager
{
    protected static string $relationship = 'milestones';

    protected static ?string $title = 'Milestones';

    public function table(Table $table): Table
    {
        return MilestonesTable::configure($table)
            ->reorderable('order')
            ->defaultSort('order')
            ->headerActions([
                CreateAction::make()
                    ->schema([
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
                        Forms\Components\Toggle::make('is_completed')
                            ->label('Completed')
                            ->default(false),
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('order')
                            ->numeric()
                            ->helperText('Change order to resequence milestone'),
                        Forms\Components\Toggle::make('is_completed')
                            ->label('Completed'),
                    ]),
                DeleteAction::make(),
            ])
            ->emptyStateHeading('No milestones')
            ->emptyStateDescription('Add milestones to track project progress.')
            ->emptyStateActions([
                CreateAction::make()
                    ->schema([
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
                        Forms\Components\Toggle::make('is_completed')
                            ->label('Completed')
                            ->default(false),
                    ]),
            ]);
    }
}