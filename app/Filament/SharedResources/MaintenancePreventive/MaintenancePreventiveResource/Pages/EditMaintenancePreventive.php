<?php

namespace App\Filament\SharedResources\MaintenancePreventive\MaintenancePreventiveResource\Pages;

use App\Filament\SharedResources\MaintenancePreventive\MaintenancePreventiveResource;
use App\Models\MaintenancePreventive;
use App\Models\Piece;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;

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

        $oldPieces = $record->pieces;
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
            $record->pieces()->detach($removedItems->pluck('id'));

        }

        // loop the newPieces, check if we already have that piece in the pivot table, if so and if it's updated we need to reincrement the stock then update it by the new quantite_utilisee, if the piece isn't exists we simply attach it

        // dd($oldPieces, $newPieces);
        $newPieces->each(function ($piece) use ($oldPieces, $record) {
            // check if we already have this piece
            $pieceModel = Piece::find($piece['piece_id']);
            if ($oldPieces->contains('id', $piece['piece_id'])) {
                $oldPiece = $oldPieces->where('id', $piece['piece_id'])->first();
                // increment its stock by the quantite_utilisee
                if ($pieceModel) {
                    $pieceModel->increment('quantite_stock', $oldPiece->pivot->quantite_utilisee);
                }
                // detach the old piece
                $record->pieces()->detach($piece['piece_id']);
            }
            // decrement the stock by the new quantite_utilisee
            $pieceModel->decrement('quantite_stock', $piece['quantite_utilisee']);
        });

        // now attach the new pieces
        $newPieces->each(function ($piece) use ($record) {
            $record->pieces()->attach($piece['piece_id'], [
                'quantite_utilisee' => $piece['quantite_utilisee'],
            ]);
        });

        $record->load('pieces'); // Reload the record to reflect the changes

        $technicienResponsable = User::find($this->getRecord()->user_id);
        if (isset($technicienResponsable)) {
            Notification::make()
                ->title('Maintenace Preventive Planifié')
                ->body("vous êtes affécté à un nouveau maintenance préventive Planifié ")
                ->success()
                ->actions([
                    Action::make('Voir+')
                        ->url('filament.technicien.resources.maintenance-preventive.view', $this->getRecord()->id)
                        ->icon('heroicon-o-eye'),
                ])
                ->sendToDatabase($technicienResponsable);
        }

    }
}
