<?php

namespace App\Providers\Filament;

use App\Http\Middleware\EngineerAccess;
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

class EngineerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('engineer')
            ->path('engineer')
            ->brandLogo(asset('favicon.svg'))
            ->brandLogoHeight('3rem')
            ->colors([
                'primary' => Color::Blue
            ])
            ->plugins([FilamentFullCalendarPlugin::make()
                ->schedulerLicenseKey('')
                ->selectable()
                ->timezone(config('app.timezone'))
                ->locale(config('app.locale'))
                ->config([
                    'eventResizableFromStart' => true, // Permet le redimensionnement depuis le début
                    'resizable' => true,
                ])
            ])
            ->databaseNotifications()
            ->databaseNotificationsPolling('1s')
            ->discoverResources(in: app_path('Filament/Engineer/Resources'), for: 'App\\Filament\\Engineer\\Resources')
            ->discoverResources(in: app_path('Filament/SharedResources/Ticket'), for: 'App\\Filament\\SharedResources\\Ticket')
            ->discoverResources(in: app_path('Filament/SharedResources/MaintenancePreventive'), for: 'App\\Filament\\SharedResources\\MaintenancePreventive')
            ->discoverResources(in: app_path('Filament/SharedResources/Piece'), for: 'App\\Filament\\SharedResources\\Piece')
            ->discoverResources(in: app_path('Filament/SharedResources/Equipement'), for: 'App\\Filament\\SharedResources\\Equipement')
            ->discoverResources(in: app_path('Filament/SharedResources/TypeEquipement'), for: 'App\\Filament\\SharedResources\\TypeEquipement')
            ->discoverResources(in: app_path('Filament/SharedResources/Bloc'), for: 'App\\Filament\\SharedResources\\Bloc')
            -> discoverResources(in: app_path('Filament/SharedResources/MaintenanceCorrective'), for: 'App\\Filament\\SharedResources\\MaintenanceCorrective')
            ->discoverResources(in: app_path('Filament/SharedResources/TypeBloc'), for: 'App\\Filament\\SharedResources\\TypeBloc')
            ->discoverPages(in: app_path('Filament/SharedPages/Pages'), for: 'App\\Filament\\SharedPages\\Pages')
            ->discoverPages(in: app_path('Filament/Engineer/Pages'), for: 'App\\Filament\\Engineer\\Pages')
            ->pages([
                // Pages\Dashboard::class,
                // \App\Filament\SharedPages\Pages\MaintenanceCorrectif::class,
            ])
            ->discoverWidgets(in: app_path('Filament/SharedWidgets/Widgets'), for: 'App\\Filament\\SharedWidgets\\Widgets')
            // ->discoverWidgets(in: app_path('Filament/Engineer/Widgets'), for: 'App\\Filament\\Engineer\\Widgets')
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
