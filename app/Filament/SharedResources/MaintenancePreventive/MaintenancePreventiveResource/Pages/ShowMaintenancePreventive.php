<?php

namespace App\Filament\SharedResources\MaintenancePreventive\MaintenancePreventiveResource\Pages;

use App\Filament\SharedResources\MaintenancePreventive\MaintenancePreventiveResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Storage;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Carbon\Carbon;

class ShowMaintenancePreventive extends ViewRecord
{
    protected static string $resource = MaintenancePreventiveResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informations de base')
                    ->schema([
                        Infolists\Components\TextEntry::make('equipement.designation')
                            ->label('Équipement')
                            ->formatStateUsing(fn ($record) => $record->equipement?->designation . ' - ' . $record->equipement?->modele . ' - ' . $record->equipement?->marque)
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('typeMaintenance.nom')
                            ->label('Type de maintenance')
                            ->badge(),
                        Infolists\Components\TextEntry::make('statut')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'planifiee' => 'info',
                                'en_attente' => 'warning',
                                'en_cours' => 'primary',
                                'termine' => 'success',
                                'reportee' => 'gray',
                                'annulee' => 'danger',
                            }),
                        Infolists\Components\TextEntry::make('type_externe')
                            ->label('Type de maintenance')
                            ->getStateUsing(fn ($record) => $record->type_externe ? 'Externe' : 'Interne')
                            ->badge()
                            ->color(fn ($state) => $state === 'Externe' ? 'success' : 'warning'),
                    ])->columns(3),

                Infolists\Components\Section::make('Détails de la maintenance')
                    ->schema([
                        Infolists\Components\TextEntry::make('description')
                            ->label('Description')
                            ->markdown()
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('date_planifiee')
                            ->label('Date planifiée')
                            ->dateTime('d/m/Y H:i'),
                        Infolists\Components\TextEntry::make('date_realisation')
                            ->label('Date de réalisation')
                            ->dateTime('d/m/Y H:i'),
                        Infolists\Components\TextEntry::make('frequence')
                            ->label('Fréquence'),
                        Infolists\Components\TextEntry::make('fournisseur')
                            ->label('Fournisseur')
                            ->visible(fn ($record) => $record->type_externe),
                    ])->columns(2),

                Infolists\Components\Section::make('Assignation')
                    ->schema([
                        Infolists\Components\TextEntry::make('createur.name')
                            ->label('Créé par')
                            ->getStateUsing(fn ($record) => $record->createur?->name . ' ' . $record->createur?->prenom),
                        Infolists\Components\TextEntry::make('assignee.name')
                            ->label('Assigné à')
                            ->getStateUsing(fn ($record) => $record->assignee?->name . ' ' . $record->assignee?->prenom),
                    ])->columns(2),

                Infolists\Components\Section::make('Actions réalisées')
                    ->schema([
                        Infolists\Components\TextEntry::make('actions_realisees')
                            ->label('Actions réalisées')
                            ->markdown()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => $record->statut === 'termine'),

                Infolists\Components\Section::make('Pièces utilisées')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('pieces')
                            ->schema([
                                Infolists\Components\TextEntry::make('designation')
                                    ->label('Désignation'),
                                Infolists\Components\TextEntry::make('pivot.quantite_utilisee')
                                    ->label('Quantité utilisée'),
                                Infolists\Components\TextEntry::make('reference')
                                    ->label('Référence'),
                                Infolists\Components\TextEntry::make('prix_unitaire')
                                    ->label('Prix unitaire')
                                    ->money('EUR'),
                            ])
                            ->columns(4)
                    ])
                    ->visible(fn ($record) => $record->pieces->isNotEmpty()),

                Infolists\Components\Section::make('Rapport d\'intervention')
                    ->schema([
                        Infolists\Components\TextEntry::make('rapport_type')
                            ->label('Type de rapport')
                            ->badge()
                            ->formatStateUsing(fn ($state) => $state === 'manuel' ? 'Upload manuel' : 'Génération automatique')
                            ->visible(fn ($record) => $record->statut === 'termine'),
                        Infolists\Components\Actions::make([
                            Infolists\Components\Actions\Action::make('view_rapport')
                                ->label('Voir le rapport')
                                ->icon('heroicon-o-document-text')
                                ->url(fn ($record) => $record->rapport_path ? Storage::url($record->rapport_path) : null)
                                ->visible(fn ($record) => $record->rapport_path !== null && $record->statut === 'termine' && $record->rapport_type === 'manuel')
                                ->color('primary')
                                ->button(),
                            Infolists\Components\Actions\Action::make('download_rapport')
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
            Actions\EditAction::make(),
            Action::make('close')
                ->label('Fermer')
                ->url(route('filament.' . auth()->user()->role . '.resources.maintenance-preventives.index'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
        ];
    }
}

