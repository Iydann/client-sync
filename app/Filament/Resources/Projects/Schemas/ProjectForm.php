<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\ProjectStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('client_id')
                    ->relationship('client', 'company_name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Client'),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->columnSpanFull(),
                Select::make('status')
                    ->options(ProjectStatus::class)
                    ->required()
                    ->default('pending'),
                DatePicker::make('deadline'),
            ]);
    }
}
