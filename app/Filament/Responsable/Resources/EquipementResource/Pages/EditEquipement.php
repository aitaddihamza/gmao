<?php

namespace App\Filament\Responsable\Resources\EquipementResource\Pages;

use App\Filament\Responsable\Resources\EquipementResource;
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
