<?php

namespace App\Filament\Resources\ProjectRequests\Pages;

use App\Filament\Resources\ProjectRequests\ProjectRequestResource;
use App\Filament\Traits\HasGlobalYearFilter;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListProjectRequests extends ListRecords
{
    protected static string $resource = ProjectRequestResource::class;

    use HasGlobalYearFilter;

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();
        $year = session('project_year', now()->year);

        if ($year && $year !== 'all') {
            $query->whereYear('created_at', $year);
        }

        return $query;
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
