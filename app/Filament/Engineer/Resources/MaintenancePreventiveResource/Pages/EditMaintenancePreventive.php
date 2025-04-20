<?php

namespace App\Filament\Engineer\Resources\MaintenancePreventiveResource\Pages;

use App\Models\MaintenancePreventive;
use App\Filament\Engineer\Resources\MaintenancePreventiveResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMaintenancePreventive extends EditRecord
{
    protected static string $resource = MaintenancePreventiveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // Similar to the create page, handle pieces_utilisees before saving
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['pieces_utilisees'])) {
            $this->pieces = $data['pieces_utilisees'];
            unset($data['pieces_utilisees']);
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $record = $this->getRecord();

        // Handle pieces synchronization if we have pieces data
        if (isset($this->pieces)) {
            $pieceData = [];
            foreach ($this->pieces as $piece) {
                if (!empty($piece['piece_id']) && isset($piece['quantite_utilisee'])) {
                    $pieceData[$piece['piece_id']] = ['quantite_utilisee' => $piece['quantite_utilisee']];
                }
            }

            $record->pieces()->sync($pieceData);
        }
    }
}
