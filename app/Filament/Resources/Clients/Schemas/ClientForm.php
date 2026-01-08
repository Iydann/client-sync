<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
// import roles
use Spatie\Permission\Models\Role;


class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('user_id')
                    ->relationship('user', 'name', fn ($query) => $query->whereHas('roles', fn ($query) => $query->where('name', 'client')))
                    ->searchable()
                    ->required()
                    ->preload()
                    ->label('User (Client)'),
                    TextInput::make('company_name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->tel()
                    ->maxLength(20),
                Textarea::make('address')
                    ->columnSpanFull(),
            ]);
    }
}
