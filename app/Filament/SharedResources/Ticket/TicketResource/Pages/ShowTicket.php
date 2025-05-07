<?php

namespace App\Filament\SharedResources\Ticket\TicketResource\Pages;

use App\Filament\SharedResources\Ticket\TicketResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\ImageEntry;
use Carbon\Carbon;
use Filament\Actions;

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
                        ImageEntry::make('chemin_image')
                            ->label('Images de panne')
                            ->columnSpanFull()
                            ->stacked()
                            ->circular()
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
                        TextEntry::make('date_attribution')
                            ->label('Date d\'attribution')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('date_cloture')
                            ->label('Date de clôture')
                            ->dateTime('d/m/Y H:i'),
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
                            ->label('temps d\'arrêt')
                            ->getStateUsing(function ($record) {
                                if ($record->date_intervention && $record->date_resolution) {
                                    $intervention = Carbon::parse($record->date_intervention);
                                    $resolution = Carbon::parse($record->date_resolution);
                                    return $intervention->diffInHours($resolution) . ' heures';
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
