<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use App\Models\User;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use App\ClientType;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('client_type')
                    ->label('Client Type')
                    ->options(ClientType::class)
                    ->required()
                    ->default(ClientType::Individual->value),
                TextInput::make('client_name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->tel()
                    ->maxLength(20)
                    ->columnSpan('full'),
                Textarea::make('address')
                    ->columnSpanFull(),

                Section::make('User Information')
                    ->description('Login credentials for the client')
                    ->schema([
                        // --- TOGGLE UNDANGAN ---
                        Toggle::make('send_invitation')
                            ->label('Send invitation via Email? (Client sets their own password)')
                            ->default(true)
                            ->live()
                            ->columnSpanFull(),

                        TextInput::make('user.name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('user.email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(User::class, 'email')
                            ->maxLength(255),

                        // --- PASSWORD KONDISIONAL ---
                        TextInput::make('user.password')
                            ->label('Password')
                            ->password()
                            ->required(fn ($get) => !$get('send_invitation'))
                            ->hidden(fn ($get) => $get('send_invitation'))
                            ->maxLength(255),
                    ])
                    ->columnSpanFull()
                    ->hidden(fn ($operation) => $operation === 'edit'),
            ]);
    }
}