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
use App\Http\Middleware\ResponsableAccess;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;

class ResponsablePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('responsable')
            ->path('responsable')
            ->brandName('GMAO')
            ->databaseNotifications()
            ->plugins([FilamentFullCalendarPlugin::make()
                    ->selectable()
                    ->editable()
            ])
            ->colors([
                'primary' => Color::Teal,
            ])
            ->discoverResources(in: app_path('Filament/Responsable/Resources'), for: 'App\\Filament\\Responsable\\Resources')
            ->discoverResources(in: app_path('Filament/SharedResources/Ticket'), for: 'App\\Filament\\SharedResources\\Ticket')
            ->discoverResources(in: app_path('Filament/SharedResources/Piece'), for: 'App\\Filament\\SharedResources\\Piece')
            ->discoverResources(in: app_path('Filament/SharedResources/Bloc'), for: 'App\\Filament\\SharedResources\\Bloc')
            ->discoverResources(in: app_path('Filament/SharedResources/MaintenancePreventive'), for: 'App\\Filament\\SharedResources\\MaintenancePreventive')
            ->discoverResources(in: app_path('Filament/SharedResources/MaintenanceCorrective'), for: 'App\\Filament\\SharedResources\\MaintenanceCorrective')
            ->discoverPages(in: app_path('Filament/Responsable/Pages'), for: 'App\\Filament\\Responsable\\Pages')
            ->discoverPages(in: app_path('Filament/SharedPages/Pages'), for: 'App\\Filament\\SharedPages\\Pages')
            ->discoverResources(in: app_path('Filament/SharedResources/Equipement'), for: 'App\\Filament\\SharedResources\\Equipement')
            ->pages([
                // Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/SharedWidgets/Widgets'), for: 'App\\Filament\\SharedWidgets\\Widgets')
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
                ResponsableAccess::class
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
