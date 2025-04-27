<?php

namespace App\Filament\Responsable\Resources\TypeEquipementResource\Pages;

use App\Filament\Responsable\Resources\TypeEquipementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTypeEquipements extends ListRecords
{
    protected static string $resource = TypeEquipementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}