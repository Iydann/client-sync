<?php

namespace App\Filament\Resources\Milestones\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;

class MilestoneForm
{
    public static function configure(Schema $schema): Schema
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
                    ->default(false)
                    ->columnSpanFull(),
            ]);
    }
}
