<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use App\Models\Project; 
use Illuminate\Support\Carbon;
use App\Http\Middleware\SetGlobalProjectYear;
use Illuminate\Support\Facades\Cache;

class AdminPanelProvider extends PanelProvider
{
    public static function getNavigationGroupName(): string
    {
        if (!Auth::check()) {
            return "Project Management";
        }

        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        return $user?->hasRole('client') ? "Project Portal" : "Project Management";
    }

    public function panel(Panel $panel): Panel
    {
        $groupName = self::getNavigationGroupName();

        return $panel
            ->default()
            ->id('admin')
            ->path('portal')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login()
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->passwordReset()
            ->navigationGroups([
                $groupName,
                "Analytics",
                "Settings",
            ])
            ->renderHook(
                PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
                fn(): HtmlString => new HtmlString(
                    view('filament.pages.year-filter', [
                        'currentYear' => session('project_year', now()->year),
                        'years' => $this->getProjectYears(),
                    ])->render()
                )
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                // Dashboard::class, (Hapus bawaan dashboard, karena sudah dibuat di page)
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                // AccountWidget::class,
                // FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                SetGlobalProjectYear::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make()
                    ->navigationGroup("Settings"),
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    protected function getProjectYears(): array
    {
        // Dibungkus dengan Cache
        // Data akan disimpan selama 1 hari (1440 menit) atau sampai cache dibersihkan.
        // php artisan cache:clear
        return Cache::remember('project_years_list', 60 * 24, function () {
            try {
                $minDate = Project::min('start_date');
                
                $minYear = $minDate ? Carbon::parse($minDate)->year : now()->year;
                $maxYear = now()->year;

                if ($minYear > $maxYear) {
                    $minYear = $maxYear;
                }

                return range($maxYear, $minYear);

            } catch (\Exception $e) {
                return range(now()->year, now()->year);
            }
        });
    }
}
