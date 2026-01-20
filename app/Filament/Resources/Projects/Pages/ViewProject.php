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
            EditAction::make(),

            Action::make('uploadAssets')
                ->label('Upload Assets')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('primary')
                ->schema([
                    SpatieMediaLibraryFileUpload::make('assets')
                        ->collection('project-assets')
                        ->multiple()
                        ->maxFiles(10)
                        ->openable()
                        ->previewable(),
                ])
                ->action(function (array $data) {
                    collect($data['assets'] ?? [])
                        ->each(fn ($file) =>
                            $this->record->addMedia($file)->toMediaCollection('project-assets')
                        );

                    $this->record->refresh();

                    Notification::make()
                        ->title('Assets uploaded')
                        ->success()
                        ->send();
                }),
        ];
    }
}
