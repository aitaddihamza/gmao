<?php

namespace App\Filament\SharedResources\Ticket\TicketResource\Pages;

use App\Filament\SharedResources\Ticket\TicketResource;
use App\Models\User;
use App\Models\Piece;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;

class EditTicket extends EditRecord
{
    protected static string $resource = TicketResource::class;



    protected function mutateFormDataBeforeFill(array $data): array
    {
        $firstTicket = $this->getRecord()->first();
        // dd($firstTicket->equipement->etat);
        $record = $this->getRecord();
        $record->load('pieces'); // Load associated pieces

        // Store the original state in the class property
        $this->originalPiecesState = collect($record->pieces->map(function ($piece) {
            return [
                'piece_id' => $piece->id,
                'quantite_utilisee' => $piece->pivot->quantite_utilisee,
            ];
        })->toArray());

        $data['pieces_utilisees'] = $this->originalPiecesState->toArray();

        return $data;
    }

    protected function afterSave(): void
    {
        $ticket = $this->getRecord();

        // Notify the assigned user
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

        // gestion des pièces

        $oldPieces = $ticket->pieces;
        // les nouveaux pieces utilisées
        $newPieces = collect($this->pieces);
        // dd($oldPieces, $newPieces);
        // get the removed pieces
        $removedItems = $oldPieces->whereNotIn('id', $newPieces->pluck('piece_id'));

        if (count($removedItems) > 0) {
            // dd($removedItems);
            // first we need to reincrement theire stock
            $removedItems->each(function ($removedItem) {
                $piece = Piece::find($removedItem->id);
                if ($piece) {
                    $piece->increment('quantite_stock', $removedItem->pivot->quantite_utilisee);
                }
            });
            // second we need to detached theme from this MaintenancePreventive
            $ticket->pieces()->detach($removedItems->pluck('id'));

        }

        // loop the newPieces, check if we already have that piece in the pivot table, if so and if it's updated we need to reincrement the stock then update it by the new quantite_utilisee, if the piece isn't exists we simply attach it

        // dd($oldPieces, $newPieces);
        $newPieces->each(function ($piece) use ($oldPieces, $ticket) {
            // check if we already have this piece
            $pieceModel = Piece::find($piece['piece_id']);
            if ($oldPieces->contains('id', $piece['piece_id'])) {
                $oldPiece = $oldPieces->where('id', $piece['piece_id'])->first();
                // increment its stock by the quantite_utilisee
                if ($pieceModel) {
                    $pieceModel->increment('quantite_stock', $oldPiece->pivot->quantite_utilisee);
                }
                // detach the old piece
                $ticket->pieces()->detach($piece['piece_id']);
            }
            // decrement the stock by the new quantite_utilisee
            $pieceModel->decrement('quantite_stock', $piece['quantite_utilisee']);
        });

        // now attach the new pieces
        $newPieces->each(function ($piece) use ($ticket) {
            $ticket->pieces()->attach($piece['piece_id'], [
                'quantite_utilisee' => $piece['quantite_utilisee'],
            ]);
        });

        $ticket->load('pieces'); // Reload the ticket to reflect the changes

        $technicienResponsable = User::find($this->getRecord()->user_id);
        if (isset($technicienResponsable)) {
            Notification::make()
                ->title('Maintenance Préventive Planifiée')
                ->body("Vous êtes affecté à une nouvelle maintenance préventive planifiée")
                ->success()
                ->actions([
                    Action::make('Voir plus')
                        ->url(route('filament.'.$technicienResponsable->role.'.resources.maintenance-preventive.view', $this->getRecord()->id))
                        ->icon('heroicon-o-eye'),
                ])
                ->sendToDatabase($technicienResponsable);
        }


        $equipement = $this->getRecord()->equipement;
        $equipementEtat = $this->data['equipement_etat'];
        if ($equipementEtat) {
            $equipement->update(['etat' => $equipementEtat]);
        }

        if ($ticket->type_ticket == "correctif") {
            $equipement = $ticket->equipement;
            // notifier tous les utilisateurs n'import qeul role par ce panne de ce équipement
            foreach (User::all() as $user) {
                if ($user->role == 'admin' || $user->id == auth()->user()->id || $ticket->user_assignee_id == $user->id) {
                    continue;
                }
                Notification::make()
                    ->title('Équipement Hors Service')
                    ->body("L'équipement {$equipement->designation} est hors service.")
                    ->success()
                    ->actions([
                        Action::make('Voir l\'Équipement')
                            ->url(route('filament.' . $user->role . '.resources.equipements.view', $equipement->id))
                            ->icon('heroicon-o-eye'),
                    ])
                    ->sendToDatabase($user);
            }
        }


    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['pieces_utilisees'])) {
            $this->pieces = $data['pieces_utilisees'];
            unset($data['pieces_utilisees']);
        }
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
