<?php

namespace App\Filament\Technicien\Resources\TicketResource\Pages;

use App\Filament\Technicien\Resources\TicketResource;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditTicket extends EditRecord
{
    protected static string $resource = TicketResource::class;

    protected function afterSave(): void
    {
        $ticket = $this->getRecord();

        // Notify the assigned user
        if ($ticket->user_assignee_id) {
            $assignee = User::find($ticket->user_assignee_id);

            if ($assignee) {
                Notification::make()
                    ->title('Ticket Assigned')
                    ->body("You have been assigned to ticket ID: {$ticket->id} for equipment {$ticket->equipement->designation}.")
                    ->success()
                    ->actions([
                        Action::make('View Ticket')
                            ->url(route('filament.'.$assignee->role.'.resources.tickets.show', $ticket->id))
                            ->icon('heroicon-o-eye'),
                    ])
                    ->sendToDatabase($assignee);
            }
        }
    }
}
