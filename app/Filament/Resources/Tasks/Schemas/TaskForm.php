<?php

namespace App\Filament\Resources\Tasks\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('milestone_id')
                    ->relationship('milestone', 'name')
                    ->required(),

                TextInput::make('order')
                    ->numeric(),

                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Textarea::make('description')
                    ->columnSpanFull()
                    ->rows(4),

                SpatieMediaLibraryFileUpload::make('attachments')
                    ->collection('task-attachments')
                    ->multiple()
                    ->previewable()
                    ->openable()
                    ->downloadable()
                    ->maxFiles(5)
                    ->label('Attachments')
                    ->columnSpanFull(),

                Toggle::make('is_completed')
                    ->label('Completed'),
            ]);
    }
}
