<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class DashboardTechnicien extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.dashboard-technicien';


    public static function canAccess(): bool
    {
        return auth()->user()?->role === 'technicien';
    }

    public static function getNavigationLabel(): string
    {
        return 'Tableau de bord';
    }
}
