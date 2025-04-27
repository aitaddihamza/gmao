<?php

namespace App\Filament\SharedResources\Bloc\BlocResource\Pages;

use App\Filament\SharedResources\Bloc\BlocResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBloc extends ViewRecord
{
    protected static string $resource = BlocResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

