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

class AdminPanelProvider extends PanelProvider
{
    /**
     * Get the navigation group name based on user role
     */
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
            ->login()
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->navigationGroups([
                $groupName,
                "Analytics",
                "Settings",
            ])
            ->renderHook(
                PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
                fn(): HtmlString => new HtmlString(
                    view('filament.pages.year-filter', [
                        'currentYear' => request()->integer('year', now()->year),
                        'years' => range(now()->year, now()->year - 5),
                    ])->render()
                )
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
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
            ])
            ->plugins([
                FilamentShieldPlugin::make()
                    ->navigationGroup("Settings"),
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
