<?php

namespace App\Filament\Responsable\Pages;

use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.responsable.pages.dashboard';
    // change the navigation label to "Tableau de bord"
    protected static ?string $navigationLabel = 'Tableau de bord';


    

}
