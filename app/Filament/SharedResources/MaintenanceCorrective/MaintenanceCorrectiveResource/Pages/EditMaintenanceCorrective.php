<?php

namespace App\Filament\SharedResources\MaintenanceCorrective\MaintenanceCorrectiveResource\Pages;

use App\Filament\SharedResources\MaintenanceCorrective\MaintenanceCorrectiveResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMaintenanceCorrective extends EditRecord
{
    protected static string $resource = MaintenanceCorrectiveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
