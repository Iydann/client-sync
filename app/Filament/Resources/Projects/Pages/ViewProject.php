<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Edit Details')
                ->icon('heroicon-o-pencil'),

            Action::make('uploadAssets')
                ->label('Manage Assets')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('primary')
                ->fillForm(fn() => [
                    'assets' => [],
                ])
                ->form([
                    SpatieMediaLibraryFileUpload::make('assets')
                        ->label('Project Assets')
                        ->collection('project-assets')
                        ->multiple()
                        ->maxFiles(20)
                        ->reorderable()
                        ->downloadable()
                        ->openable()
                        ->previewable(false)
                        ->uploadingMessage('Uploading...')
                        ->helperText('Upload new files, remove or reorder existing ones.'),
                ])
                ->action(function () {
                    Notification::make()
                        ->title('Assets updated successfully')
                        ->success()
                        ->send();
                }),
        ];
    }
}
