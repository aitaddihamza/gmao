<?php

namespace App\Filament\SharedResources\Piece\PieceResource\Pages;

use App\Filament\SharedResources\Piece\PieceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPieces extends ListRecords
{
    protected static string $resource = PieceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
