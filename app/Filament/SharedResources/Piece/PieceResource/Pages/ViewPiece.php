<?php

namespace App\Filament\SharedResources\Piece\PieceResource\Pages;

use App\Filament\SharedResources\Piece\PieceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Support\Facades\Storage;

class ViewPiece extends ViewRecord
{
    protected static string $resource = PieceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informations générales')
                    ->schema([
                        Infolists\Components\TextEntry::make('designation'),
                        Infolists\Components\TextEntry::make('reference'),
                        Infolists\Components\TextEntry::make('quantite_stock')
                            ->label('Quantité en stock'),
                        Infolists\Components\TextEntry::make('fournisseur'),
                        Infolists\Components\TextEntry::make('prix_unitaire')
                            ->label('Prix unitaire')
                            ->money('MAD'),
                    ])->columns(2),

                Infolists\Components\Section::make('Manuel d\'utilisation')
                    ->schema([
                        Infolists\Components\Actions::make([
                            Infolists\Components\Actions\Action::make('view_manuel')
                                ->label('Voir le manuel')
                                ->icon('heroicon-o-document-text')
                                ->url(fn ($record) => $record->manuel_path ? Storage::url($record->manuel_path) : null)
                                ->visible(fn ($record) => $record->manuel_path !== null)
                                ->color('primary')
                                ->button(),
                        ]),
                    ]),
            ]);
    }
} 