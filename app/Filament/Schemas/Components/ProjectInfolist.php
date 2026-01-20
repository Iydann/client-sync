<?php

namespace App\Filament\Schemas\Components;

use App\Models\Project;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Project Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('title')
                            ->label('Title'),
                        TextEntry::make('client.client_name')
                            ->label('Client'),
                        TextEntry::make('status')
                            ->badge(),
                        TextEntry::make('progress')
                            ->label('Progress')
                            ->formatStateUsing(fn ($state) => $state . '%'),
                        TextEntry::make('payment_progress')
                            ->label('Payment Progress')
                            ->formatStateUsing(fn ($state) => $state . '%'),
                        TextEntry::make('contract_value')
                            ->label('Contract Value')
                            ->formatStateUsing(fn ($state) => 'IDR ' . number_format($state, 0, ',', '.')),
                        TextEntry::make('contract_number')
                            ->label('Contract Number')
                            ->placeholder('-'),
                        TextEntry::make('contract_date')
                            ->date(),
                        TextEntry::make('deadline')
                            ->date(),
                        TextEntry::make('created_at')
                            ->label('Created')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Updated')
                            ->dateTime(),
                        TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull()
                            ->markdown(),
                    ]),
                Section::make('Assets')
                    ->collapsible()
                    ->schema([
                        TextEntry::make('assets_list')
                            ->label('Uploaded Assets')
                            ->state(function (Project $record) {
                                $media = $record->getMedia('project-assets');
                                if ($media->isEmpty()) {
                                    return 'Belum ada aset diunggah';
                                }

                                return $media
                                    ->map(fn ($item) => sprintf(
                                        '<a href="%s" target="_blank" rel="noopener" class="text-primary-600 hover:underline">%s</a>',
                                        $item->getFullUrl(),
                                        e($item->file_name)
                                    ))
                                    ->implode('<br>');
                            })
                            ->html()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
