<?php

namespace App\Filament\SharedResources\TicketResource\Pages;

use App\Filament\SharedResources\TicketResource;
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
