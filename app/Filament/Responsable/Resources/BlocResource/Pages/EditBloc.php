<?php

namespace App\Filament\Responsable\Resources\BlocResource\Pages;

use App\Filament\Responsable\Resources\BlocResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBloc extends EditRecord
{
    protected static string $resource = BlocResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
