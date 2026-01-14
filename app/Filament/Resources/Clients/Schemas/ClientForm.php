<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
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
                Section::make('User Information')
                    ->description('Login credentials for the client')
                    ->schema([
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
                        TextInput::make('user.password')
                            ->label('Password')
                            ->password()
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(2)
                    ->hidden(fn ($operation) => $operation === 'edit'),
                
                Section::make('Company Information')
                    ->schema([
                        Select::make('client_type')
                            ->label('Client Type')
                            ->options(ClientType::class)
                            ->required()
                            ->default(ClientType::Individual->value),
                        TextInput::make('company_name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->tel()
                            ->maxLength(20),
                        Textarea::make('address')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}

