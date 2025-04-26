<?php

namespace App\Filament\Engineer\Resources\TicketResource\Pages;

use App\Filament\Engineer\Resources\TicketResource;
use Filament\Resources\Pages\ViewRecord;

class ShowTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Add any actions you want to display on the show page, if needed
        ];
    }
}
