<?php

namespace App\Filament\SharedResources\Ticket\TicketResource\Pages;

use App\Filament\SharedResources\Ticket\TicketResource;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    protected function afterCreate(): void
    {
        $ticket = $this->getRecord();

        // Notify the assigned user if a user was assigned
        if ($ticket->user_assignee_id) {
            $assignee = User::find($ticket->user_assignee_id);
            $url = route("filament.".$assignee->role.".resources.tickets.show", $ticket->id);

            if ($assignee) {
                Notification::make()
                    ->title('Ticket Assigned')
                    ->body("You have been assigned to ticket ID: {$ticket->id} for equipment {$ticket->equipement->designation}.")
                    ->success()
                    ->actions([
                        Action::make('View Ticket')
                            ->url($url)
                            ->icon('heroicon-o-eye'),
                    ])
                    ->sendToDatabase($assignee);
            }
        }
    }
}
