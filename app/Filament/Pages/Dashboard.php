<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Traits\HasGlobalYearFilter;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    use HasGlobalYearFilter;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';
    // protected static ?string $title = 'Dashboard Utama';

    public static function canAccess(): bool
    {
        return Auth::check();
    }
}