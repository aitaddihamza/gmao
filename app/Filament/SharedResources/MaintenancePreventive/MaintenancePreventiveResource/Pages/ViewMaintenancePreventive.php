<?php

namespace App\Filament\SharedResources\MaintenancePreventive\MaintenancePreventiveResource\Pages;

use App\Filament\SharedResources\MaintenancePreventive\MaintenancePreventiveResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Actions as InfolistActions;
use Illuminate\Support\Facades\Storage;
use Filament\Infolists\Components\Actions\Action;
use Filament\Actions as PageActions;
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
                            ->formatStateUsing(fn ($record) => $record->equipement?->designation . ' - ' . $record->equipement?->modele . ' - ' . $record->equipement?->marque)
                            ->columnSpanFull(),
                        // l'état de l'équipement
                        TextEntry::make('equipement.etat')
                            ->label('État de l\'équipement')
                            ->getStateUsing(fn ($record) => $record->equipement?->etat)
                            ->badge()
                            ->color(fn ($state) => match ($state) {
                                'en_service' => 'success',
                                'hors_service' => 'danger',
                                'en_maintenance' => 'warning',
                                default => 'gray',
                            }),
                        TextEntry::make('bloc.localisation')
                            ->label('Localisation de l\'équipement')
                            ->getStateUsing(fn ($record) => $record->equipement?->bloc->localisation)
                            ->badge()
                            ->color('secondary'),
                        TextEntry::make('statut')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'planifiee' => 'info',
                                'en_attente' => 'warning',
                                'en_cours' => 'primary',
                                'termine' => 'success',
                                'reportee' => 'gray',
                                'annulee' => 'danger',
                            }),
                        TextEntry::make('type_externe')
                            ->label('Type de maintenance')
                            ->getStateUsing(fn ($record) => $record->type_externe ? 'Externe' : 'Interne')
                            ->badge()
                            ->color(fn ($state) => $state === 'Externe' ? 'success' : 'warning'),
                    ])->columns(3),

                Section::make('Détails de la maintenance')
                    ->schema([
                        TextEntry::make('description')
                            ->label('Description')
                            ->markdown()
                            ->columnSpanFull(),
                        TextEntry::make('date_debut')
                            ->label('Date début')
                            ->dateTime('d/m/Y H:i')
                            ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->format('d/m/Y H:i') : '-'),
                        TextEntry::make('date_fin')
                            ->label('Date fin')
                            ->dateTime('d/m/Y H:i')
                            ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->format('d/m/Y H:i') : '-'),
                        TextEntry::make('fournisseur')
                            ->label('Fournisseur')
                            ->visible(fn ($record) => $record->type_externe),
                    ])->columns(2),

                Section::make('Assignation')
                    ->schema([
                        TextEntry::make('createur.name')
                            ->label('Créé par')
                            ->getStateUsing(fn ($record) => $record->createur?->name . ' ' . $record->createur?->prenom),
                        TextEntry::make('assignee.name')
                            ->label('Assigné à')
                            ->getStateUsing(fn ($record) => $record->assignee?->name . ' ' . $record->assignee?->prenom),
                    ])->columns(2),

                Section::make('Actions réalisées')
                    ->schema([
                        TextEntry::make('actions_realisees')
                            ->label('Actions réalisées')
                            ->markdown()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => $record->statut === 'termine'),

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
                                TextEntry::make('prix_unitaire')
                                    ->label('Prix unitaire')
                                    ->money('EUR'),
                            ])
                            ->columns(4)
                    ])
                    ->visible(fn ($record) => $record->pieces->isNotEmpty()),

                Section::make('Rapport d\'intervention')
                    ->schema([
                        TextEntry::make('rapport_type')
                            ->label('Type de rapport')
                            ->badge()
                            ->formatStateUsing(fn ($state) => $state === 'manuel' ? 'Upload manuel' : 'Génération automatique')
                            ->visible(fn ($record) => $record->statut === 'termine'),
                        InfolistActions::make([
                            Action::make('view_rapport')
                                ->label('Voir le rapport')
                                ->icon('heroicon-o-document-text')
                                ->url(fn ($record) => $record->rapport_path ? Storage::url($record->rapport_path) : null)
                                ->visible(fn ($record) => $record->rapport_path !== null && $record->statut === 'termine' && $record->rapport_type === 'manuel')
                                ->color('primary')
                                ->button(),
                            Action::make('download_rapport')
                                ->label('Télécharger le rapport Word')
                                ->icon('heroicon-o-arrow-down-tray')
                                ->url(fn ($record) => $record->rapport_path ? Storage::url($record->rapport_path) : null)
                                ->visible(fn ($record) => $record->rapport_path !== null && $record->statut === 'termine' && $record->rapport_type === 'auto')
                                ->color('success')
                                ->button(),
                        ])
                        ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->visible(fn ($record) => $record->statut === 'termine'),
            ]);
    }


    protected function getHeaderActions(): array
    {
        return [
            PageActions\EditAction::make(),
            PageActions\Action::make('close')
                ->label('Fermer')
                ->url(route('filament.' . auth()->user()->role . '.resources.maintenance-preventive.index'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
        ];
    }

}
