<?php

namespace App\Filament\Engineer\Resources\MaintenancePreventiveResource\Pages;

use App\Models\MaintenancePreventive;
use App\Models\Piece;
use App\Filament\Engineer\Resources\MaintenancePreventiveResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditMaintenancePreventive extends EditRecord
{
    protected static string $resource = MaintenancePreventiveResource::class;

    // Add a property to store the original pieces state
    protected $originalPiecesState;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
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

        // Handle pieces synchronization
        if (isset($this->pieces)) {
            $pieceData = [];
            $newPiecesState = collect($this->pieces);

            // Ensure $this->originalPiecesState is not null
            $removedItems = $this->originalPiecesState
                ? $this->originalPiecesState->whereNotIn('piece_id', $newPiecesState->pluck('piece_id'))
                : collect();

            foreach ($removedItems as $removedItem) {
                $pieceId = $removedItem['piece_id'];
                $quantiteUtilisee = $removedItem['quantite_utilisee'];

                if ($pieceId && $quantiteUtilisee > 0) {
                    $piece = \App\Models\Piece::find($pieceId);
                    if ($piece) {
                        // Restore the stock of the removed piece
                        $piece->increment('quantite_stock', $quantiteUtilisee);
                    }
                }
            }

            // Process the new state for synchronization
            foreach ($this->pieces as $piece) {
                if (!empty($piece['piece_id']) && isset($piece['quantite_utilisee'])) {
                    $pieceId = $piece['piece_id'];
                    $quantite = $piece['quantite_utilisee'];
                    $pieceData[$pieceId] = ['quantite_utilisee' => $quantite];

                    // Update stock
                    $pieceObj = \App\Models\Piece::find($pieceId);
                    if ($pieceObj) {
                        $ancienneQuantite = $record->pieces->where('id', $pieceId)->first()?->pivot->quantite_utilisee ?? 0;
                        $pieceObj->quantite_stock += $ancienneQuantite - $quantite;
                        $pieceObj->save();
                    }
                }
            }

            $record->pieces()->sync($pieceData);
        }

        $record->load('pieces'); // Reload the record to ensure updated values
    }
}
