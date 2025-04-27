<?php

namespace App\Filament\SharedResources\TypeEquipement\TypeEquipementResource\Pages;

use App\Filament\SharedResources\TypeEquipement\TypeEquipementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTypeEquipement extends EditRecord
{
    protected static string $resource = TypeEquipementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
} 