<?php

namespace App\Filament\SharedResources\Equipement\EquipementResource\Pages;

use App\Filament\SharedResources\Equipement\EquipementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEquipements extends ListRecords
{
    protected static string $resource = EquipementResource::class;


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
