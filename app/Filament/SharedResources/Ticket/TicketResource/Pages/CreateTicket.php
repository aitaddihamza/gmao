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
            $userRole = $assignee->role;
            $url = route("filament.".$userRole.".resources.tickets.show", $ticket->id);

            if ($assignee) {
                Notification::make()
                    ->title('Ticket Assigné')
                    ->body("Vous avez été assigné au ticket ID: {$ticket->id} pour l'équipement {$ticket->equipement->designation}.")
                    ->success()
                    ->actions([
                        Action::make('Voir le Ticket')
                            ->url($url)
                            ->icon('heroicon-o-eye'),
                    ])
                    ->sendToDatabase($assignee);
            }
        }


        // Vérifier si nous avons des pièces à synchroniser
        if (isset($this->pieces)) {
            // Préparer les données des pièces pour la synchronisation
            $pieceData = [];
            foreach ($this->pieces as $piece) {
                if (!empty($piece['piece_id']) && isset($piece['quantite_utilisee'])) {
                    $pieceId = $piece['piece_id'];
                    $quantite = $piece['quantite_utilisee'];
                    $pieceData[$pieceId] = ['quantite_utilisee' => $quantite];

                    // Mettre à jour le stock de la pièce
                    $pieceObj = Piece::find($pieceId);
                    if ($pieceObj) {
                        $pieceObj->quantite_stock -= $quantite;
                        $pieceObj->save();
                    }
                }
            }

            // Synchroniser les pièces avec l'enregistrement de maintenance
            $ticket->pieces()->sync($pieceData);
            // le temps arret = $date_resolution - date d'intervention
            $temps_arret = $ticket->date_resolution->diffInHours($ticket->date_intervention);
            $ticket->temps_arret = $temps_arret;
        }

        // change l'état de l'équipement
        if ($ticket->type_ticket == "correctif") {
            $equipement = $ticket->equipement;
            $equipement->update(['etat' => 'hors_service']);
            // dd($equipement);
        }
    }

}
