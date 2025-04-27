<?php

namespace App\Filament\Responsable\Resources\TypeEquipementResource\Pages;

use App\Filament\Responsable\Resources\TypeEquipementResource;
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