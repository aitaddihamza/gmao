<?php

namespace App\Filament\SharedResources\Equipement\EquipementResource\Pages;

use App\Filament\SharedResources\Equipement\EquipementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Carbon\Carbon;

class EditEquipement extends EditRecord
{
    protected static string $resource = EquipementResource::class;

    protected function getHeaderActions(): array
    {
        // $now = Carbon::now()->addDay()->toDateString();
        // $date_planifiee = Carbon::parse($this->getRecord()->maintenancePreventives->first()->date_planifiee->toString())->toDateString();
        // dd($now == ($date_planifiee));
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
