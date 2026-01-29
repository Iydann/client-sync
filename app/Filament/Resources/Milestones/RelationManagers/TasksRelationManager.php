<?php

namespace App\Filament\Resources\Milestones\RelationManagers;

use App\Models\Task;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    protected static ?string $title = 'Tasks';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('order')
                    ->numeric()
                    ->default(function () {
                        return $this->getOwnerRecord()->tasks()->max('order') + 1;
                    })
                    ->columnSpanFull(),

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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->reorderable('order')
            ->defaultSort('order', 'asc')
            ->recordAction('view')
            ->columns([
                TextColumn::make('order')
                    ->sortable()
                    ->width(50),

                TextColumn::make('name')
                    ->searchable()
                    ->label('Task Name - Description')
                    ->weight('bold')
                    ->description(fn ($record) => \Illuminate\Support\Str::limit($record->description, 30)),

                TextColumn::make('media_count') 
                    ->counts('media')
                    ->label('Attachments')
                    ->badge()
                    ->color('info')
                    ->alignCenter(),

                IconColumn::make('is_completed')
                    ->boolean(),

                TextColumn::make('contributors.name')
                    ->label('Contributors')
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-m-user')
                    ->separator('') 
                    ->extraAttributes([
                        'class' => 'flex flex-col gap-1 items-start', 
                    ])
                    ->limitList(3)
                    ->expandableLimitedList(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = Auth::id();
                        return $data;
                    })
                    ->after(function (Task $record) {
                        $record->contributors()->syncWithoutDetaching([Auth::id()]);
                    }),
            ])
            ->actions([
                ViewAction::make(),
                
                EditAction::make()
                    ->after(function (Task $record) {
                        $record->contributors()->syncWithoutDetaching([Auth::id()]);
                    }),
                
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}