<?php

namespace App\Filament\Engineer\Pages;

use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';


    protected static string $view = 'filament.engineer.pages.dashboard';

    protected static ?string $navigationLabel = 'Tableau de bord';
}
