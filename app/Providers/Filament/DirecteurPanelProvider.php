<?php

namespace App\Providers\Filament;

use App\Http\Middleware\ChefAccess;
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
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;

class DirecteurPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('directeur')
            ->path('directeur')
            ->brandLogo(asset('favicon.svg'))
            ->brandLogoHeight('3rem')
             ->plugins([FilamentFullCalendarPlugin::make()
                    ->selectable()
                    ->editable()
            ])
            ->databaseNotifications()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Directeur/Resources'), for: 'App\\Filament\\Directeur\\Resources')
            ->discoverPages(in: app_path('Filament/Directeur/Pages'), for: 'App\\Filament\\Directeur\\Pages')
            ->discoverWidgets(in: app_path('Filament/Directeur/Widgets'), for: 'App\\Filament\\Directeur\\Widgets')
            ->discoverResources(in: app_path('Filament/SharedResources/Piece'), for: 'App\\Filament\\SharedResources\\Piece')
            ->discoverResources(in: app_path('Filament/SharedResources/Ticket'), for: 'App\\Filament\\SharedResources\\Ticket')
            ->discoverResources(in: app_path('Filament/SharedResources/Equipement'), for: 'App\\Filament\\SharedResources\\Equipement')
            ->discoverResources(in: app_path('Filament/SharedResources/TypeEquipement'), for: 'App\\Filament\\SharedResources\\TypeEquipement')
            ->discoverResources(in: app_path('Filament/SharedResources/Bloc'), for: 'App\\Filament\\SharedResources\\Bloc')
            ->discoverResources(in: app_path('Filament/SharedResources/TypeBloc'), for: 'App\\Filament\\SharedResources\\TypeBloc')
            ->discoverResources(in: app_path('Filament/SharedResources/MaintenancePreventive'), for: 'App\\Filament\\SharedResources\\MaintenancePreventive')
            ->discoverResources(in: app_path('Filament/SharedResources/MaintenanceCorrective'), for: 'App\\Filament\\SharedResources\\MaintenanceCorrective')
            ->discoverWidgets(in: app_path('Filament/Majeur/Widgets'), for: 'App\\Filament\\Majeur\\Widgets')
            ->discoverPages(in: app_path('Filament/SharedPages/Pages'), for: 'App\\Filament\\SharedPages\\Pages')
            ->discoverPages(in: app_path('Filament/SharedPages/Pages'), for: 'App\\Filament\\SharedPages\\Pages')
            ->widgets([
                Widgets\AccountWidget::class,
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
                ChefAccess::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
