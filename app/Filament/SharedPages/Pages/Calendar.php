<?php

namespace App\Filament\SharedPages\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use App\Filament\SharedResources\MaintenancePreventive\MaintenancePreventiveResource;

class Calendar extends Page
{
    // update the icon to calendar
    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static string $view = 'filament.sharedpages.pages.calendar';

    // make the calender under the maintenace group and under the mainteance preventive

    protected static ?string $navigationGroup = 'Maintenance';
    // change the title or the lable
    protected static ?string $title = 'Calendrier';



}
