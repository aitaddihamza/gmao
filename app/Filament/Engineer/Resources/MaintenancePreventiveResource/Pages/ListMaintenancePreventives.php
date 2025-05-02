<?php

namespace App\Filament\Engineer\Resources\MaintenancePreventiveResource\Pages;

use App\Models\Piece;
use App\Filament\Engineer\Resources\MaintenancePreventiveResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\DeleteAction;

class ListMaintenancePreventives extends ListRecords
{
    protected static string $resource = MaintenancePreventiveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Planifier')
                ->icon('heroicon-o-plus')
                ->url(MaintenancePreventiveResource::getUrl('create'))
                ->color('primary'),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function ($record) {
                    // Restore stock for all pieces used in this maintenance preventive
                    foreach ($record->pieces as $piece) {
                        $quantiteUtilisee = $piece->pivot->quantite_utilisee;
                        if ($quantiteUtilisee > 0) {
                            $piece->increment('quantite_stock', $quantiteUtilisee);
                        }
                    }
                }),
        ];
    }
}
