<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Traits\HasGlobalYearFilter;

class ListClients extends ListRecords
{
    protected static string $resource = ClientResource::class;

    use HasGlobalYearFilter;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
