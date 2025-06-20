<?php

namespace App\Filament\SharedPages\Pages;

use Filament\Pages\Page;

class Calendar extends Page
{
    // update the icon to calendar
    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static string $view = 'filament.shared.pages.calendar';

    // make the calender under the maintenace group and under the mainteance preventive

    protected static ?string $navigationGroup = 'Maintenance';
    // change the title or the lable
    protected static ?string $title = 'Calendrier';


    protected function getHeaderActions(): array
    {
        return [];
        // engineer role can create maintenance preventive
        // if (auth()->user()->role != "engineer") {
        //
        // }
        // return [
        //     Action::make('create')
        //         ->label('Planifier')
        //         ->icon('heroicon-o-plus')
        //         ->url(MaintenancePreventiveResource::getUrl('create'))
        //         ->color('primary')
        //     ,
        // ];
    }

}
