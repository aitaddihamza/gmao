<?php

namespace App\Filament\Engineer\Resources\MaintenancePreventiveResource\Pages;

use App\Filament\Engineer\Resources\MaintenancePreventiveResource;
use App\Models\Piece;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

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
    }
}
