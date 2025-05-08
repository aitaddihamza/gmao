<?php

namespace App\Filament\SharedResources\Ticket\TicketResource\Pages;

use App\Filament\SharedResources\Ticket\TicketResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Actions;
use Carbon\Carbon;
use Filament\Actions as PageActions;
use Illuminate\Support\Facades\Storage;
use Filament\Infolists\Components\Actions\Action;

class ShowTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informations de base')
                    ->schema([
                        TextEntry::make('equipement.designation')
                            ->label('Équipement')
                            ->columnSpanFull(),
                        TextEntry::make('type_ticket')
                            ->label('Type de ticket')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'correctif' => 'danger',
                                'installation' => 'success',
                                'formation' => 'info',
                                default => 'gray',
                            }),
                        TextEntry::make('priorite')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'critique' => 'danger',
                                'haute' => 'warning',
                                'moyenne' => 'info',
                                default => 'gray',
                            }),
                        TextEntry::make('statut')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'nouveau' => 'gray',
                                'attribue' => 'info',
                                'en_cours' => 'warning',
                                'cloture' => 'success',
                                default => 'danger',
                            }),
                    ])->columns(3),

                Section::make('Détails du problème')
                    ->schema([
                        TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                        TextEntry::make('gravite_panne')
                            ->label('Gravité de la panne')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'critique' => 'danger',
                                'majeure' => 'warning',
                                'mineure' => 'info',
                                'aucune' => 'success',
                                default => 'gray',
                            })
                            ->visible(fn ($record) => $record->type_ticket === 'correctif'),
                        ImageEntry::make('chemin_image')
                            ->label('Images de panne')
                            ->columnSpanFull()
                            ->stacked()
                            ->circular(false)
                            ->width('full')
                            ->height(300)
                            ->visible(fn ($record) => $record->chemin_image !== null),
                    ]),

                Section::make('Recommandations assisté par AI')
                    ->schema([
                        TextEntry::make('recommandations')
                            ->label('')
                            ->columnSpanFull(),
                    ]),

                Section::make('Assignation')
                    ->schema([
                        TextEntry::make('createur.name')
                            ->label('Créé par')
                            ->getStateUsing(fn ($record) => $record->createur?->name . ' ' . $record->createur?->prenom),
                        TextEntry::make('assignee.name')
                            ->label('Assigné à')
                            ->getStateUsing(fn ($record) => $record->assignee?->name . ' ' . $record->assignee?->prenom),
                    ])->columns(2),

                Section::make('Rapport et Résolution')
                    ->schema([
                        TextEntry::make('diagnostic')
                            ->label('Diagnostic')
                            ->visible(fn ($record) => $record->type_ticket === 'correctif'),
                        TextEntry::make('solution')
                            ->label('Solution')
                            ->visible(fn ($record) => $record->type_ticket === 'correctif'),
                        TextEntry::make('date_intervention')
                            ->label('Date d\'intervention')
                            ->dateTime('d/m/Y H:i')
                            ->visible(fn ($record) => $record->type_ticket === 'correctif'),
                        TextEntry::make('date_resolution')
                            ->label('Date de résolution')
                            ->dateTime('d/m/Y H:i')
                            ->visible(fn ($record) => $record->type_ticket === 'correctif'),
                        TextEntry::make('type_externe')
                            ->label('Type d\'intervention')
                            ->badge()
                            ->formatStateUsing(fn ($state) => $state ? 'Externe' : 'Interne')
                            ->visible(fn ($record) => $record->type_ticket === 'correctif'),
                        TextEntry::make('temps_arret')
                            ->label('Temps d\'arrêt')
                            ->getStateUsing(function ($record) {
                                if ($record->date_intervention && $record->date_resolution) {
                                    $intervention = Carbon::parse($record->date_intervention);
                                    $resolution = Carbon::parse($record->date_resolution);
                                    $diffInHours = $intervention->diffInHours($resolution);

                                    if ($diffInHours > 48) {
                                        $diffInDays = $intervention->diffInDays($resolution);
                                        return intval($diffInDays) . ' jours'; // Affiche uniquement les jours sans décimales ni virgule
                                    }

                                    return intval($diffInHours) . ' heures'; // Affiche les heures sans décimales ni virgule
                                }
                                return 'Non calculé';
                            }),
                        TextEntry::make('fournisseur')
                            ->label('Fournisseur')
                            ->visible(fn ($record) => $record->type_ticket === 'correctif' && $record->type_externe),
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
                    ->visible(fn ($record) => $record->type_ticket === 'correctif' && $record->pieces->isNotEmpty()),

                Section::make('Rapport d\'intervention')
                    ->schema([
                        TextEntry::make('rapport_type')
                            ->label('Type de rapport')
                            ->badge()
                            ->formatStateUsing(fn ($state) => $state === 'manuel' ? 'Upload manuel' : 'Génération automatique')
                            ->visible(fn ($record) => $record->statut === 'cloture'),
                        Actions::make([
                            Action::make('view_rapport')
                                ->label('Voir le rapport')
                                ->icon('heroicon-o-document-text')
                                ->url(fn ($record) => $record->rapport_path ? Storage::url($record->rapport_path) : null)
                                ->visible(fn ($record) => $record->rapport_path !== null && $record->statut === 'cloture' && $record->rapport_type === 'manuel')
                                ->color('primary')
                                ->button(),
                            Action::make('download_rapport')
                                ->label('Télécharger le rapport Word')
                                ->icon('heroicon-o-arrow-down-tray')
                                ->url(fn ($record) => $record->rapport_path ? Storage::url($record->rapport_path) : null)
                                ->visible(fn ($record) => $record->rapport_path !== null && $record->statut === 'cloture' && $record->rapport_type === 'auto')
                                ->color('success')
                                ->button(),
                        ])
                        ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->visible(fn ($record) => $record->statut === 'cloture'),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            PageActions\EditAction::make(),
            PageActions\Action::make('close')
                ->label('Fermer')
                ->url(route('filament.' . auth()->user()->role . '.resources.maintenance-correctives.index'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
        ];
    }
}
