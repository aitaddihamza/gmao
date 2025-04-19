<?php

namespace App\Filament\Engineer\Resources\MaintenancePreventiveResource\Pages;

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
}
