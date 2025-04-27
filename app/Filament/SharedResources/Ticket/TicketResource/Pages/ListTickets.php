<?php

namespace App\Filament\SharedResources\Ticket\TicketResource\Pages;

use App\Filament\SharedResources\Ticket\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\ViewAction::make(), // Add the "View" action
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ];
    }
}
