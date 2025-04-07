<?php

namespace App\Providers;

// app/Providers/TechnicienPanelProvider.php
use Filament\Panel;
use Filament\PanelProvider;

class TechnicienPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('technicien')
            ->path('technicien')
            ->login()
            ->discoverResources(in: app_path('Filament/Technicien/Resources'), for: 'App\\Filament\\Technicien\\Resources')
            ->discoverPages(in: app_path('Filament/Technicien/Pages'), for: 'App\\Filament\\Technicien\\Pages')
            ->middleware(['web', 'auth', 'panel.role:technicien']);
    }
}
