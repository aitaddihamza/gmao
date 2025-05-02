<?php

namespace App\Filament\Technicien\Pages;

use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.technicien.pages.dashboard';
    // change the navigation label to "Tableau de bord"
    protected static ?string $navigationLabel = 'Tableau de bord';

}
