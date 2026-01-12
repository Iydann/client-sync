<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Filament\Resources\Milestones\Schemas\MilestoneForm;
use App\Filament\Resources\Milestones\Tables\MilestonesTable;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class MilestonesRelationManager extends RelationManager
{
    protected static string $relationship = 'milestones';

    public function table(Table $table): Table
    {
        return MilestonesTable::configure($table)
            ->headerActions([
                CreateAction::make()
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Toggle::make('is_completed')
                            ->label('Completed')
                            ->default(false),
                        TextInput::make('order')
                            ->numeric()
                            ->default( function () {
                                return $this->ownerRecord->milestones()->max('order') + 1;
                            }),
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Toggle::make('is_completed')
                            ->label('Completed'),
                        TextInput::make('order')
                            ->numeric(),
                    ]),
            ]);
    }
}