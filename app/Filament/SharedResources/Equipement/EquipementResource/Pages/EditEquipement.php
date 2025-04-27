<?php

namespace App\Filament\SharedResources\Equipement\EquipementResource\Pages;

use App\Filament\SharedResources\Equipement\EquipementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEquipement extends EditRecord
{
    protected static string $resource = EquipementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
