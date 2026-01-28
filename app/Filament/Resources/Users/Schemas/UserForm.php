<?php

namespace App\Filament\Resources\Users\Schemas;

use App\ClientType;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                TextInput::make('password')
                    ->label('New Password')
                    ->password()
                    ->required(fn($operation) => $operation === 'create')
                    ->dehydrated(fn($state) => filled($state))
                    ->maxLength(255),
                Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->maxItems(1)
                    ->searchable()
                    ->live(),
                
                Section::make('Client Information')
                    ->schema([
                        Select::make('client_type')
                            ->label('Client Type')
                            ->options([
                                ClientType::Individual->value => ClientType::Individual->getLabel(),
                                ClientType::Organization->value => ClientType::Organization->getLabel(),
                            ])
                            ->required()
                            ->native(false),
                        
                        TextInput::make('client_name')
                            ->label('Client Name')
                            ->required()
                            ->maxLength(255),
                        
                        TextInput::make('phone')
                            ->label('Phone')
                            ->tel()
                            ->maxLength(255),
                        
                        Textarea::make('address')
                            ->label('Address')
                            ->rows(3)
                            ->maxLength(1000),
                    ])
                    ->relationship('client')
                    ->visible(function ($get) {
                        $roles = $get('roles');
                        if (!$roles) return false;
                        $clientRole = \Spatie\Permission\Models\Role::where('name', 'client')->first();
                        return $clientRole && in_array($clientRole->id, (array) $roles);
                    })
                    ->columnSpanFull(),
            ]);
    }
}
