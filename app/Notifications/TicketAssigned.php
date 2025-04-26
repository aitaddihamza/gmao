<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Filament\Notifications\Notification as FilamentNotification;

class TicketAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    protected Ticket $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function via($notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'Ticket Assigned',
            'message' => "You have been assigned to ticket ID: {$this->ticket->id} for equipment {$this->ticket->equipement->designation}.",
            'ticket_id' => $this->ticket->id,
            'url' => route('filament.technicien.resources.tickets.edit', $this->ticket->id),
        ];
    }

    public function toFilamentNotification($notifiable): FilamentNotification
    {
        return FilamentNotification::make()
            ->title('Ticket Assigned')
            ->body("You have been assigned to ticket ID: {$this->ticket->id} for equipment {$this->ticket->equipement->designation}.")
            ->success();
    }
}
