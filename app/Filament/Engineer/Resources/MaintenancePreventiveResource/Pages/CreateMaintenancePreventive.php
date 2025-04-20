<?php

namespace App\Filament\Engineer\Resources\MaintenancePreventiveResource\Pages;

use App\Filament\Engineer\Resources\MaintenancePreventiveResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMaintenancePreventive extends CreateRecord
{
    protected static string $resource = MaintenancePreventiveResource::class;

    protected function afterSave(): void
{
    $record = $this->getRecord();
    
    // Récupérer les données des pièces depuis le formulaire
    $pieces = $this->data['pieces_utilisees'] ?? [];
    
    // Préparer les données pour la synchronisation
    $pieceData = [];
    foreach ($pieces as $piece) {
        if (!empty($piece['piece_id']) && !empty($piece['quantite_utilisee'])) {
            $pieceData[$piece['piece_id']] = ['quantite_utilisee' => $piece['quantite_utilisee']];
        }
    }
    
    // Synchroniser les pièces avec la maintenance préventive
    $record->pieces()->sync($pieceData);
}

}
