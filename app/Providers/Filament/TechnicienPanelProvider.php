<?php

namespace App\Providers\Filament;

use App\Http\Middleware\TechnicienAcces;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
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
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;

class TechnicienPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('technicien')
            ->path('technicien')
            ->brandLogo(asset('favicon.svg'))
            ->brandLogoHeight('3rem')
            ->profile()
            ->plugins([FilamentFullCalendarPlugin::make()
                ->schedulerLicenseKey('')
                ->selectable()
                ->timezone(config('app.timezone'))
                ->locale(config('app.locale'))
                ->config([])
            ])
            ->colors([
                'primary' => Color::Yellow,
            ])
            ->databaseNotifications()
            ->databaseNotificationsPolling('1s')
            ->discoverResources(in: app_path('Filament/SharedResources/Piece'), for: 'App\\Filament\\SharedResources\\Piece')
            ->discoverResources(in: app_path('Filament/SharedResources/Equipement'), for: 'App\\Filament\\SharedResources\\Equipement')
            ->discoverResources(in: app_path('Filament/Technicien/Resources'), for: 'App\\Filament\\Technicien\\Resources')
            ->discoverResources(in: app_path('Filament/SharedResources/Bloc'), for: 'App\\Filament\\SharedResources\\Bloc')
            ->discoverResources(in: app_path('Filament/SharedResources/Ticket'), for: 'App\\Filament\\SharedResources\\Ticket')
            ->discoverResources(in: app_path('Filament/SharedResources/MaintenancePreventive'), for: 'App\\Filament\\SharedResources\\MaintenancePreventive')
            ->discoverResources(in: app_path('Filament/SharedResources/MaintenanceCorrective'), for: 'App\\Filament\\SharedResources\\MaintenanceCorrective')
            ->discoverPages(in: app_path('Filament/Technicien/Pages'), for: 'App\\Filament\\Technicien\\Pages')
            ->discoverPages(in: app_path('Filament/SharedPages/Pages'), for: 'App\\Filament\\SharedPages\\Pages')
            // ->pages([
            //     // Pages\Dashboard::class,
            // ])
            // add the shared resource ticketresource
            ->discoverWidgets(in: app_path('Filament/Technicien/Widgets'), for: 'App\\Filament\\Technicien\\Widgets')
            ->discoverWidgets(in: app_path('Filament/SharedWidgets/Widgets'), for: 'App\\Filament\\SharedWidgets\\Widgets')
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
