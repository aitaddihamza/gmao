<?php

namespace App\Filament\Responsable\Resources\TypeBlocResource\Pages;

use App\Filament\Responsable\Resources\TypeBlocResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTypeBloc extends EditRecord
{
    protected static string $resource = TypeBlocResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
} 