<?php

namespace App\Filament\SharedResources\MaintenancePreventive\MaintenancePreventiveResource\Pages;

use App\Filament\SharedResources\MaintenancePreventive\MaintenancePreventiveResource;
use App\Models\Piece;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;

class CreateMaintenancePreventive extends CreateRecord
{
    protected static string $resource = MaintenancePreventiveResource::class;



    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['pieces_utilisees'])) {
            $this->pieces = $data['pieces_utilisees'];
            unset($data['pieces_utilisees']);
        }
        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->getRecord();

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
            $record->pieces()->sync($pieceData);
        }

        $technicienResponsable = $this->getRecord()->assignee;

        if (isset($technicienResponsable)) {
            $userRole = $technicienResponsable->role;
            Notification::make()
                ->title('Maintenace Preventive Planifié')
                ->body("vous êtes affécté à un nouveau maintenance préventive Planifié ")
                ->success()
                ->actions([
                    Action::make('Voir plus')
                        ->url(route('filament.'.$userRole.'.resources.maintenance-preventive.view', $this->getRecord()->id))
                        ->icon('heroicon-o-eye'),
                ])
                ->sendToDatabase($technicienResponsable);
        }

    }
}
