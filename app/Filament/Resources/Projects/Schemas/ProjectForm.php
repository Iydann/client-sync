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
                    ->relationship('client', 'client_name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Client'),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->columnSpanFull(),
                DatePicker::make('start_date'),
                DatePicker::make('deadline'),
                DatePicker::make('contract_date'),
                TextInput::make('contract_number')
                    ->maxLength(100),
                TextInput::make('contract_value')
                    ->numeric()
                    ->minValue(0)
                    ->prefix('IDR')
                    ->required(),
                Select::make('status')
                    ->options(ProjectStatus::class)
                    ->required()
                    ->default('pending'),
            ]);
    }
}
