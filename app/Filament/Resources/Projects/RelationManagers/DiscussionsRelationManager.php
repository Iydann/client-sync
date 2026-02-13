<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Models\ProjectDiscussion;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Hidden;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class DiscussionsRelationManager extends RelationManager
{
    protected static string $relationship = 'discussions';

    protected static ?string $title = 'Discussions';

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        return (string) ($ownerRecord->discussions_count ?? $ownerRecord->discussions()->count());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('project_id')
                    ->default(fn (RelationManager $livewire) => $livewire->getOwnerRecord()->getKey())
                    ->dehydrated(),
                Hidden::make('user_id')
                    ->default(Auth::id()),

                Textarea::make('message')
                    ->required()
                    ->rows(4)
                    ->placeholder('Type your message here...')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('message')
            ->columns([
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('message')
                    ->label('Message')
                    ->searchable()
                    ->limit(50)
                    ->wrap(),

                TextColumn::make('created_at')
                    ->label('Posted At')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('New Message')
                    ->mutateFormDataUsing(function (array $data, RelationManager $livewire): array {
                        $data['project_id'] ??= $livewire->getOwnerRecord()->getKey();
                        $data['user_id'] = Auth::id();
                        return $data;
                    })
                    ->using(function (array $data, RelationManager $livewire): ProjectDiscussion {
                        return $livewire->getOwnerRecord()->discussions()->create($data);
                    }),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
