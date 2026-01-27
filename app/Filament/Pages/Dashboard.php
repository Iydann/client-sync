<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Traits\HasGlobalYearFilter;

class Dashboard extends BaseDashboard
{
    use HasGlobalYearFilter;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';
    // protected static ?string $title = 'Dashboard Utama';
}