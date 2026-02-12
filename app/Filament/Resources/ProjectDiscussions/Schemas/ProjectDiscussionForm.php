<?php

namespace App\Filament\Resources\ProjectDiscussions\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class ProjectDiscussionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('project_id')
                    ->default(fn () => request()->route('projectId') ?? request()->query('projectId')),
                Hidden::make('user_id')
                    ->default(Auth::id()),
                Textarea::make('message')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
