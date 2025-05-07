<?php

namespace App\Filament\SharedResources\MaintenancePreventive\MaintenancePreventiveResource\Pages;

use App\Filament\SharedResources\MaintenancePreventive\MaintenancePreventiveResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Carbon\Carbon;

class ViewMaintenancePreventive extends ViewRecord
{
    protected static string $resource = MaintenancePreventiveResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informations de base')
                    ->schema([
                        TextEntry::make('equipement.designation')
                            ->label('Équipement')
                            ->columnSpanFull(),
                        TextEntry::make('type_maintenance')
                            ->label('Type de maintenance')
                            ->badge()
                            ->color('success'),
                        TextEntry::make('frequence')
                            ->label('Fréquence')
                            ->badge(),
                        TextEntry::make('statut')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'planifie' => 'info',
                                'en_cours' => 'warning',
                                'termine' => 'success',
                                default => 'gray',
                            }),
                    ])->columns(3),

                Section::make('Détails de la maintenance')
                    ->schema([
                        TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                        TextEntry::make('instructions')
                            ->label('Instructions')
                            ->columnSpanFull(),
                    ]),

                Section::make('Planification')
                    ->schema([
                        TextEntry::make('date_planifiee')
                            ->label('Date planifiée')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('date_debut')
                            ->label('Date de début')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('date_fin')
                            ->label('Date de fin')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('duree_estimee')
                            ->label('Durée estimée')
                            ->getStateUsing(fn ($record) => $record->duree_estimee . ' heures'),
                    ])->columns(2),

                Section::make('Assignation')
                    ->schema([
                        TextEntry::make('createur.name')
                            ->label('Créé par')
                            ->getStateUsing(fn ($record) => $record->createur?->name . ' ' . $record->createur?->prenom),
                        TextEntry::make('technicien.name')
                            ->label('Technicien assigné')
                            ->getStateUsing(fn ($record) => $record->technicien?->name . ' ' . $record->technicien?->prenom),
                    ])->columns(2),

                Section::make('Rapport de maintenance')
                    ->schema([
                        TextEntry::make('observations')
                            ->label('Observations')
                            ->columnSpanFull(),
                        TextEntry::make('resultat')
                            ->label('Résultat')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'reussi' => 'success',
                                'echec' => 'danger',
                                default => 'gray',
                            }),
                    ])->columns(2),

                Section::make('Pièces utilisées')
                    ->schema([
                        RepeatableEntry::make('pieces')
                            ->schema([
                                TextEntry::make('designation')
                                    ->label('Désignation'),
                                TextEntry::make('pivot.quantite_utilisee')
                                    ->label('Quantité utilisée'),
                                TextEntry::make('reference')
                                    ->label('Référence'),
                            ])
                            ->columns(3)
                    ])
                    ->visible(fn ($record) => $record->pieces->isNotEmpty()),
            ]);
    }

    public function getHeaderActions(): array
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
