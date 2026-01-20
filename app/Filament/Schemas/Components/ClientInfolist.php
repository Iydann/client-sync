<?php

namespace App\Filament\Schemas\Components;

use App\Models\Client;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\ClientType;

class ClientInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Client Details')
                ->columns(2)
                ->schema([
                    TextEntry::make('client_name')->label('Client Name'),
                    TextEntry::make('user.name')->label('User Name'),
                    TextEntry::make('phone')->label('Phone'),
                    TextEntry::make('user.email')->label('Email'),
                    TextEntry::make('address')->columnSpanFull(),
                    TextEntry::make('client_type')
                        ->label('Client Type')
                        ->formatStateUsing(function ($state) {
                            if ($state instanceof ClientType) {
                                return $state->getLabel();
                            }
                            return $state;
                        })
                        ->badge(),
                ])
                // ->columnspan('full'),
        ]);
    }
}