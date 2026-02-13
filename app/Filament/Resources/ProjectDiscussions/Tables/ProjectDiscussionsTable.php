<?php

namespace App\Filament\Resources\ProjectDiscussions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProjectDiscussions\ProjectDiscussionResource;
use App\Filament\Resources\ProjectDiscussions\Pages\ViewProjectDiscussion;
use Illuminate\Support\Facades\Auth;

class ProjectDiscussionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                Project::query()
                    ->withCount('discussions')
                    ->when(Auth::user()?->hasRole('client'), function (Builder $query) {
                        $query->where('client_id', Auth::user()?->client?->id);
                    })
                    ->when(Auth::user()?->hasRole('developer'), function (Builder $query) {
                        $query->whereHas('members', function (Builder $subQuery) {
                            $subQuery->where('users.id', Auth::id());
                        });
                    })
            )
            ->columns([
                TextColumn::make('title')
                    ->label('Project')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('discussions_count')
                    ->label('Messages')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordUrl(fn (Project $record): string => ProjectDiscussionResource::getUrl('view', ['projectId' => $record->id]))
            ->toolbarActions([
                //
            ])
            ->defaultSort('discussions_count', 'desc');
    }
}
