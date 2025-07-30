<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Http\Middleware\CheckUserActive;
use App\Http\Middleware\TenantMiddleware;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AssetSurveyPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('asset-survey')
            ->path('asset-survey')
            ->login()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->brandName('Asset Survey Management')
            ->discoverResources(in: app_path('Filament/AssetSurvey/Resources'), for: 'App\\Filament\\AssetSurvey\\Resources')
            ->discoverPages(in: app_path('Filament/AssetSurvey/Pages'), for: 'App\\Filament\\AssetSurvey\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/AssetSurvey/Widgets'), for: 'App\\Filament\\AssetSurvey\\Widgets')
            ->widgets([
                // Asset Survey specific widgets will be added here
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
                TenantMiddleware::class,
                'panel.access:asset-survey',
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->authMiddleware([
                Authenticate::class,
                CheckUserActive::class,
            ])
            ->authGuard('web')
            ->loginRouteSlug('login')
            ->registration(false); // Disable registration for now
    }
}