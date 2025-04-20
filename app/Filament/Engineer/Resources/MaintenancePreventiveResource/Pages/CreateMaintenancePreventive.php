<?php

namespace App\Filament\Engineer\Resources\MaintenancePreventiveResource\Pages;

use App\Filament\Engineer\Resources\MaintenancePreventiveResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMaintenancePreventive extends CreateRecord
{
    protected static string $resource = MaintenancePreventiveResource::class;

    // This property is crucial for handling relationship data properly
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Remove the pieces_utilisees from the data to avoid errors with the create method
        if (isset($data['pieces_utilisees'])) {
            $this->pieces = $data['pieces_utilisees'];
            unset($data['pieces_utilisees']);
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->getRecord();

        // Check if we have pieces to sync
        if (isset($this->pieces)) {
            // Prepare the pieces data for syncing
            $pieceData = [];
            foreach ($this->pieces as $piece) {
                if (!empty($piece['piece_id']) && isset($piece['quantite_utilisee'])) {
                    $pieceData[$piece['piece_id']] = ['quantite_utilisee' => $piece['quantite_utilisee']];
                }
            }

            // Sync the pieces with the maintenance record
            $record->pieces()->sync($pieceData);
        }
    }
}
