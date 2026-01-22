<?php

namespace App\Filament\Resources\UserContributions;

use App\Filament\Resources\UserContributions\Pages\CreateUserContribution;
use App\Filament\Resources\UserContributions\Pages\EditUserContribution;
use App\Filament\Resources\UserContributions\Pages\ListUserContributions;
use App\Filament\Resources\UserContributions\Pages\ViewUserContribution;
use App\Filament\Resources\UserContributions\Schemas\UserContributionForm;
use App\Filament\Resources\UserContributions\Schemas\UserContributionInfolist;
use App\Filament\Resources\UserContributions\Tables\UserContributionsTable;
use App\Models\UserContribution;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UserContributionResource extends Resource
{
    protected static ?string $model = UserContribution::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Users;

    protected static ?string $navigationLabel = 'User Contributions';
    protected static string|\UnitEnum|null $navigationGroup = 'Analytics';

    public static function form(Schema $schema): Schema 
    {    
        return UserContributionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UserContributionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UserContributionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUserContributions::route('/'),
            'create' => CreateUserContribution::route('/create'),
            'view' => ViewUserContribution::route('/{record}'),
            'edit' => EditUserContribution::route('/{record}/edit'),
        ];
    }
}
