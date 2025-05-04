<?php

namespace App\Filament\SharedResources\MaintenancePreventive\MaintenancePreventiveResource\Pages;

use App\Filament\SharedResources\MaintenancePreventive\MaintenancePreventiveResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMaintenancePreventive extends ViewRecord
{
    protected static string $resource = MaintenancePreventiveResource::class;

    public function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

}
