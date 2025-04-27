<?php

namespace App\Filament\SharedResources\TypeBloc\TypeBlocResource\Pages;

use App\Filament\SharedResources\TypeBloc\TypeBlocResource;
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