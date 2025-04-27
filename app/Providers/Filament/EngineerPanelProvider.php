<?php

namespace App\Providers\Filament;

use App\Http\Middleware\EngineerAccess;
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

class EngineerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('engineer')
            ->path('engineer')
            ->brandName('GMAO')
            ->colors([
                'primary' => Color::Blue
            ])
            ->databaseNotifications()
            ->databaseNotificationsPolling('1s')
            ->discoverResources(in: app_path('Filament/Engineer/Resources'), for: 'App\\Filament\\Engineer\\Resources')
            ->discoverResources(in: app_path('Filament/SharedResources/Ticket'), for: 'App\\Filament\\SharedResources\\Ticket')
            ->discoverResources(in: app_path('Filament/SharedResources/Equipement'), for: 'App\\Filament\\SharedResources\\Equipement')
            ->discoverPages(in: app_path('Filament/Engineer/Pages'), for: 'App\\Filament\\Engineer\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Engineer/Widgets'), for: 'App\\Filament\\Engineer\\Widgets')
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
                EngineerAccess::class
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
