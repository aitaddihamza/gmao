<?php

namespace App\Filament\Engineer\Resources\TicketResource\Pages;

use App\Filament\Engineer\Resources\TicketResource;
use App\Models\User;
use App\Notifications\TicketAssigned;
use Filament\Resources\Pages\EditRecord;

class EditTicket extends EditRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Vos actions...
        ];
    }

    protected function afterSave(): void
    {
        $ticket = $this->getRecord();

        // Si le ticket a été assigné à un nouvel utilisateur
        if ($ticket->wasChanged('user_assignee_id') && $ticket->user_assignee_id) {
            $assignee = User::find($ticket->user_assignee_id);
            if ($assignee) {
                $assignee->notify(new TicketAssigned($ticket));
            }
        }
    }
}
