<?php

namespace App\Providers\Filament;

use App\Http\Middleware\TechnicienAcces;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
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

class TechnicienPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('technicien')
            ->path('technicien')
            ->colors([
                'primary' => Color::Green,
                'gray' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/Technicien/Resources'), for: 'App\\Filament\\Technicien\\Resources')
            ->discoverPages(in: app_path('Filament/Technicien/Pages'), for: 'App\\Filament\\Technicien\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Technicien/Widgets'), for: 'App\\Filament\\Technicien\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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
                TechnicienAcces::class
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
