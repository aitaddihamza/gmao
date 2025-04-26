<?php

namespace App\Filament\Engineer\Resources\TicketResource\Pages;

use App\Filament\Engineer\Resources\TicketResource;
use App\Models\User;
use App\Notifications\TicketAssigned;
use Filament\Resources\Pages\CreateRecord;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    protected function afterCreate(): void
    {
        $ticket = $this->getRecord();

        // Vérifier si un utilisateur a été assigné
        if ($ticket->user_assignee_id) {
            $assignee = User::find($ticket->user_assignee_id);
            dd($assignee);

            if ($assignee) {
                $assignee->notify(new TicketAssigned($ticket));
            }
        }
    }
}
