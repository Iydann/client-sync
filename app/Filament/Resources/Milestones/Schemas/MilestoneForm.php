<?php

namespace App\Filament\Resources\Milestones\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;

class MilestoneForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('project_id')
                    ->label('Project')
                    ->searchable()
                    ->required()
                    ->relationship('project', 'title')
                    ->preload(),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Toggle::make('is_completed')
                    ->label('Completed')
                    ->default(false),
                TextInput::make('order')
                    ->numeric()
                    ->default(0),
            ]);
    }
}
