<?php

namespace App\Filament\Resources\ProjectRequests\Schemas;

use App\Filament\Resources\ProjectRequests\ProjectRequestResource;
use App\ProjectRequestStatus;
use App\ProjectRequestType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class ProjectRequestForm
{
    public static function getTypeField(): Select
    {
        return Select::make('type')
            ->label('Request Type')
            ->options(self::enumOptions(ProjectRequestType::class))
            ->required();
    }

    /**
     * Base form fields shared between resource form and relation manager.
     * Does NOT include project_id (the relation manager doesn't need it).
     */
    public static function getBaseFields(): array
    {
        $isAdmin = ProjectRequestResource::isAdminUser();

        return [
            TextInput::make('title')
                ->required()
                ->maxLength(255),
            Textarea::make('description')
                ->rows(5)
                ->required()
                ->columnSpanFull(),
            SpatieMediaLibraryFileUpload::make('attachments')
                ->collection('request-attachments')
                ->multiple()
                ->previewable()
                ->openable()
                ->maxFiles(5)
                ->label('Attachments')
                ->columnSpanFull(),
            Select::make('status')
                ->options(self::enumOptions(ProjectRequestStatus::class))
                ->default(ProjectRequestStatus::Pending->value)
                ->required()
                ->hidden(fn () => !$isAdmin)
                ->disabled(fn () => !$isAdmin),
        ];
    }

    /**
     * Shared table columns used by both the resource table and the relation manager.
     */
    public static function getTableColumns(bool $showProject = false): array
    {
        $columns = [];

        $columns[] = TextColumn::make('title')
            ->label('Title')
            ->searchable()
            ->sortable()
            ->wrap();

        if ($showProject) {
            $columns[] = TextColumn::make('project.title')
                ->label('Project')
                ->searchable()
                ->sortable();

            $columns[] = TextColumn::make('client.client_name')
                ->label('Client')
                ->searchable()
                ->sortable()
                ->visible(fn () => !ProjectRequestResource::isClientUser());
        }

        $columns[] = TextColumn::make('type')
            ->label('Type')
            ->badge()
            ->sortable();

        $columns[] = TextColumn::make('status')
            ->badge()
            ->sortable();

        $columns[] = TextColumn::make('last_message_at')
            ->label('Last update')
            ->dateTime()
            ->sortable();

        return $columns;
    }

    public static function enumOptions(string $enumClass): array
    {
        return collect($enumClass::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->getLabel()])
            ->all();
    }
}
