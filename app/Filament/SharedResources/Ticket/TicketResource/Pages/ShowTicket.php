<?php

namespace App\Filament\SharedResources\Ticket\TicketResource\Pages;

use App\Filament\SharedResources\Ticket\TicketResource;
use Filament\Resources\Pages\ViewRecord;

class ShowTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        // add close and edit buttons when showing ticket

        return [
            //
        ];
    }
}
