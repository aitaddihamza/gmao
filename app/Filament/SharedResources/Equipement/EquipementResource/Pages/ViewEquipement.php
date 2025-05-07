<?php

namespace App\Filament\SharedResources\Equipement\EquipementResource\Pages;

use App\Filament\SharedResources\Equipement\EquipementResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Support\Facades\Storage;

class ViewEquipement extends ViewRecord
{
    protected static string $resource = EquipementResource::class;


    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informations générales')
                    ->schema([
                        Infolists\Components\TextEntry::make('bloc.nom_bloc')
                            ->label('Bloc'),
                        Infolists\Components\TextEntry::make('designation'),
                        Infolists\Components\TextEntry::make('marque'),
                        Infolists\Components\TextEntry::make('modele'),
                        Infolists\Components\TextEntry::make('numero_serie')
                            ->label('Numéro de série'),
                    ])->columns(2),

                Infolists\Components\Section::make('Dates importantes')
                    ->schema([
                        Infolists\Components\TextEntry::make('date_acquisition')
                            ->date()
                            ->label('Date d\'acquisition'),
                        Infolists\Components\TextEntry::make('date_mise_en_service')
                            ->date()
                            ->label('Date de mise en service'),
                        Infolists\Components\TextEntry::make('date_fin_garantie')
                            ->date()
                            ->label('Date de fin de garantie'),
                    ])->columns(3),

                Infolists\Components\Section::make('État et maintenance')
                    ->schema([
                        Infolists\Components\TextEntry::make('etat')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'bon' => 'success',
                                'acceptable' => 'warning',
                                'mauvais' => 'danger',
                                'hors_service' => 'danger',
                                default => 'gray',
                            }),
                        Infolists\Components\TextEntry::make('criticite')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'haute' => 'danger',
                                'moyenne' => 'warning',
                                'basse' => 'success',
                                default => 'gray',
                            }),
                        Infolists\Components\IconEntry::make('sous_contrat')
                            ->boolean()
                            ->label('Sous contrat'),
                    ])->columns(3),

                Infolists\Components\Section::make('Informations contractuelles')
                    ->schema([
                        Infolists\Components\TextEntry::make('type_contrat')
                            ->label('Type de contrat'),
                        Infolists\Components\TextEntry::make('numero_contrat')
                            ->label('Numéro de contrat'),
                        Infolists\Components\TextEntry::make('fournisseur'),
                        Infolists\Components\TextEntry::make('contact_fournisseur')
                            ->label('Contact fournisseur'),
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

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            // close action
            Actions\Action::make('close')
                ->label('Fermer')
                // filament.technicien.resources.maintenance-correctives.index
                ->url(route('filament.' . auth()->user()->role . '.resources.maintenance-correctives.index'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
        ];
    }


}
