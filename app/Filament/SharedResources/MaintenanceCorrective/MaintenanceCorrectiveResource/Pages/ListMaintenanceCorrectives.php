<?php

namespace App\Filament\SharedResources\MaintenanceCorrective\MaintenanceCorrectiveResource\Pages;

use App\Filament\SharedResources\MaintenanceCorrective\MaintenanceCorrectiveResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMaintenanceCorrectives extends ListRecords
{
    protected static string $resource = MaintenanceCorrectiveResource::class;


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    //

}
