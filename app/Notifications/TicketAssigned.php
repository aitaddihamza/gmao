<?php

namespace App\Notifications;

use App\Models\Ticket;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Database\DatabaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TicketAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    protected Ticket $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        $prioriteColors = [
            'critique' => 'danger',
            'haute' => 'warning',
            'moyenne' => 'info',
            'basse' => 'gray',
        ];

        return DatabaseNotification::make()
            ->title('Nouveau ticket assigné')
            ->icon('heroicon-o-clipboard-document-list')
            ->iconColor($prioriteColors[$this->ticket->priorite] ?? 'primary')
            ->body("Un ticket pour l'équipement {$this->ticket->equipement->designation} vous a été assigné.")
            ->actions([
                Action::make('voir')
                    ->button()
                    ->url(route('filament.technicien.resources.tickets.edit', $this->ticket->id))
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
