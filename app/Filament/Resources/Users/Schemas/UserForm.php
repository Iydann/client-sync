<?php

namespace App\Filament\Resources\Users\Schemas;

use App\ClientType;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle; // Import Toggle
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
                    ->maxLength(255)
                    ->unique(table: 'users', column: 'email', ignoreRecord: true)
                    ->validationMessages([
                        'unique' => 'This email address is already registered.',
                    ]),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    // Wajib HANYA JIKA: Create User DAN (Undangan Mati ATAU Bukan Client)
                    ->required(fn($operation, $get) => 
                        $operation === 'create' && !$get('send_invitation')
                    )
                    // Sembunyi JIKA: Toggle Undangan Aktif
                    ->visible(fn ($get) => !$get('send_invitation'))
                    ->dehydrated(fn($state) => filled($state))
                    ->maxLength(255),

                Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->maxItems(1)
                    ->searchable()
                    ->required()
                    ->live(), // Live agar bisa mentrigger perubahan di bawahnya

                Toggle::make('send_invitation')
                    ->label(fn ($operation) => $operation === 'create'
                        ? 'Send Invitation Email to Set Password'
                        : 'Resend Invitation / Reset Password Email'
                    )
                    ->default(false)
                    ->live()
                    ->columnSpanFull()
                    ->visible(function ($get) {
                        // Cek apakah role yang dipilih adalah 'client'
                        $roles = $get('roles');
                        if (!$roles) return false;
                        $clientRole = \Spatie\Permission\Models\Role::where('name', 'client')->first();
                        return $clientRole && in_array($clientRole->id, (array) $roles);
                    }),
                
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