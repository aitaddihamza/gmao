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

    protected function afterSave(): void
    {
        $record = $this->getRecord();
        
        // Récupérer les données des pièces depuis le formulaire
        $pieces = $this->data['pieces_utilisees'] ?? [];
        
        // Préparer les données pour la synchronisation
        $pieceData = [];
        foreach ($pieces as $piece) {
            if (!empty($piece['piece_id']) && isset($piece['quantite_utilisee'])) {
                $pieceData[$piece['piece_id']] = ['quantite_utilisee' => $piece['quantite_utilisee']];
            }
        }
        
        // Synchroniser les pièces avec la maintenance préventive
        $record->pieces()->sync($pieceData);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();

        // Populate all fields with current values
        $data['equipement_id'] = $record->equipement_id;
        $data['user_id'] = $record->user_id;
        $data['date_planifiee'] = $record->date_planifiee;
        $data['date_reelle'] = $record->date_reelle;
        $data['statut'] = $record->statut;
        $data['description'] = $record->description;
        $data['periodicite_jours'] = $record->periodicite_jours;
        $data['remarques'] = $record->remarques;

        // Handle repeater field for pieces
        $pieces = $record->relationLoaded('pieces') ? $record->pieces : $record->pieces()->get();
        $data['pieces_utilisees'] = $pieces->map(function ($piece) {
            return [
                'piece_id' => $piece->id,
                'quantite_utilisee' => $piece->pivot->quantite_utilisee,
            ];
        })->toArray();

        return $data;
    }

    public function mount($record): void
    {
        parent::mount($record);

        // Fetch the full record object
        $maintenancePreventive = MaintenancePreventive::with('pieces')->find($record);

        if ($maintenancePreventive) {
            // Populate all fields with current values
            $this->form->fill([
                'equipement_id' => $maintenancePreventive->equipement_id,
                'user_id' => $maintenancePreventive->user_id,
                'date_planifiee' => $maintenancePreventive->date_planifiee,
                'date_reelle' => $maintenancePreventive->date_reelle,
                'statut' => $maintenancePreventive->statut,
                'description' => $maintenancePreventive->description,
                'periodicite_jours' => $maintenancePreventive->periodicite_jours,
                'remarques' => $maintenancePreventive->remarques,
                'pieces_utilisees' => $maintenancePreventive->pieces->map(function ($piece) {
                    return [
                        'piece_id' => $piece->id,
                        'quantite_utilisee' => $piece->pivot->quantite_utilisee,
                    ];
                })->toArray(),
            ]);
        }
    }

}
