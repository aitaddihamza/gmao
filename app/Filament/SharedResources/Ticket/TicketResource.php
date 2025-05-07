<?php

namespace App\Filament\SharedResources\Ticket;

use App\Filament\SharedResources\Ticket\TicketResource\Pages;
use App\Models\Ticket;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations de base')
                    ->schema([
                        Forms\Components\Select::make('equipement_id')
                            ->relationship('equipement', 'designation')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->designation . ' - ' . $record->modele)
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Équipement concerné'),

                        Forms\Components\Select::make('type_ticket')
                            ->options([
                                'correctif' => 'Maintenance corrective',
                                'installation' => 'Installation',
                                'formation' => 'Formation',
                                'autre' => 'Autre',
                            ])
                            ->required()
                            ->default('correctif'),
                        Forms\Components\Select::make('priorite')
                            ->options([
                                'critique' => 'Critique - Urgent',
                                'haute' => 'Haute',
                                'moyenne' => 'Moyenne',
                                'basse' => 'Basse',
                            ])
                            ->required()
                            ->default('moyenne'),

                        Forms\Components\Select::make('statut')
                            ->options([
                                'nouveau' => 'Nouveau',
                                'attribue' => 'Attribué',
                                'en_cours' => 'En cours',
                                'en_attente' => 'En attente',
                                'cloture' => 'Clôturé',
                            ])
                            ->required()
                            ->default('nouveau')
                            ->live(),

                        Forms\Components\Select::make('equipement_etat')
                            ->label('Etat de l\'equipement')
                            ->hidden(fn (Forms\Get $get) => $get('statut') != 'cloture' || $get('type_ticket') != 'correctif')
                            ->options([
                                'bon' => 'Bon',
                                'acceptable' => 'Acceptable',
                                'mauvais' => 'Mauvais',
                                'hors_service' => 'Hors service',
                            ]),
                        Forms\Components\Textarea::make('diagnostic')
                            ->required()
                            ->hidden(fn (Forms\Get $get) => $get('statut') != 'cloture' || $get('type_ticket') != 'correctif'),
                        Forms\Components\Textarea::make('solution')
                            ->required()
                            ->placeholder('Déscription de la solution')
                            ->hidden(fn (Forms\Get $get) => $get('statut') != 'cloture' || $get('type_ticket') != 'correctif'),
                        Forms\Components\DateTimePicker::make('date_intervention')
                            ->hidden(fn (Forms\Get $get) => $get('statut') != 'cloture' || $get('type_ticket') != 'correctif')
                            ->default(now()),
                        Forms\Components\DateTimePicker::make('date_resolution')
                            ->hidden(fn (Forms\Get $get) => $get('statut') != 'cloture' || $get('type_ticket') != 'correctif')
                            ->default(now()),
                        Forms\Components\Toggle::make('type_externe')
                            ->label('Intervention est externe ?')
                            ->reactive()
                            ->hidden(fn (Forms\Get $get) => $get('type_ticket') != 'correctif'),
                        Forms\Components\TextInput::make('fournisseur')
                            ->hidden(fn (Forms\Get $get) => !$get('type_externe')),

                        Forms\Components\Repeater::make('pieces_utilisees')
                            ->schema([
                                Forms\Components\Select::make('piece_id')
                                    ->options(\App\Models\Piece::pluck('designation', 'id'))
                                    ->label('Pièce')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    ->afterStateUpdated(
                                        fn ($state, callable $set) =>
                                        $set('stock_disponible', \App\Models\Piece::find($state)?->quantite_stock ?? 0)
                                    ),
                                Forms\Components\TextInput::make('quantite_utilisee')
                                    ->required()
                                    ->numeric()
                                    ->label('Quantité utilisée')
                                    ->minValue(1)
                                    ->reactive()
                                    ->rules([
                                        function (Forms\Get $get, $record) {
                                            return function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                                                $pieceId = $get('piece_id');
                                                if (!$pieceId) {
                                                    return;
                                                }

                                                $piece = \App\Models\Piece::find($pieceId);
                                                if (!$piece) {
                                                    return;
                                                }

                                                $quantiteDejaUtilisee = 0;
                                                if ($record) {
                                                    $piecePivot = $record->pieces->where('id', $pieceId)->first()?->pivot;
                                                    if ($piecePivot) {
                                                        $quantiteDejaUtilisee = $piecePivot->quantite_utilisee;
                                                    }
                                                }

                                                $stockDisponible = $piece->quantite_stock + $quantiteDejaUtilisee;

                                                if ($value > $stockDisponible) {
                                                    $fail("La quantité demandée ({$value}) dépasse le stock disponible ({$stockDisponible}).");
                                                }
                                            };
                                        }
                                    ]),
                                Forms\Components\TextInput::make('stock_disponible')
                                    ->label('Stock disponible')
                                    ->disabled()
                                    ->dehydrated(false),
                            ])
                            ->columns(3)
                            ->createItemButtonLabel('Ajouter une pièce')
                            ->default(function ($record) {
                                if (!$record) {
                                    return [];
                                }

                                return $record->pieces->map(function ($piece) {
                                    return [
                                        'piece_id' => $piece->id,
                                        'quantite_utilisee' => $piece->pivot->quantite_utilisee,
                                        'stock_disponible' => $piece->quantite_stock + $piece->pivot->quantite_utilisee,
                                    ];
                                })->toArray();
                            })
                            ->itemLabel(function (array $state): ?string {
                                if (!isset($state['piece_id'])) {
                                    return null;
                                }

                                $piece = \App\Models\Piece::find($state['piece_id']);
                                return ($piece ? $piece->designation : 'Pièce inconnue') .
                                    ' - Qté: ' . ($state['quantite_utilisee'] ?? '0');
                            })
                            ->disableLabel(false)
                            ->afterStateHydrated(function ($state, $record) use (&$originalPiecesState) {
                                if (!$record) {
                                    return;
                                }

                                // Store the original state for comparison during save
                                $originalPiecesState = collect($record->pieces->map(function ($piece) {
                                    return [
                                        'piece_id' => $piece->id,
                                        'quantite_utilisee' => $piece->pivot->quantite_utilisee,
                                    ];
                                })->toArray());
                            }),
                    ]),


                Forms\Components\Section::make('Détails du problème')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('chemin_image')
                            ->image()
                            ->multiple()
                            ->maxFiles(5)
                            ->directory('tickets')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Assignation')
                    ->schema([
                    Forms\Components\Hidden::make('user_createur_id')
                       ->default(fn () => auth()->id())
                       ->required(),

                        Forms\Components\Select::make('user_assignee_id')
                            ->label('Technicien assigné')
                            ->relationship('assignee', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn (Forms\Get $get): bool =>
                                in_array($get('statut'), ['attribue', 'en_cours', 'en_attente', 'cloture'])),

                        Forms\Components\DateTimePicker::make('date_attribution')
                            ->visible(fn (Forms\Get $get): bool =>
                                in_array($get('statut'), ['attribue', 'en_cours', 'en_attente', 'cloture']))
                            ->default(now()),

                        Forms\Components\DateTimePicker::make('date_cloture')
                            ->visible(fn (Forms\Get $get): bool =>
                                $get('statut') === 'cloture')
                            ->default(now()),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('equipement.designation')
                    ->sortable(query: function ($query, $direction) {
                        return $query->orderBy('equipement.designation', $direction);
                    })
                    ->searchable()
                    ->getStateUsing(fn ($record) => $record->equipement?->designation . ' - ' . $record->equipement?->modele)
                    ->label('Équipement concerné')
                    ->url(fn ($record) => route('filament.engineer.resources.tickets.show', $record->id)),

                Tables\Columns\TextColumn::make('createur.name')
                    ->label('Créé par')
                    ->sortable(query: function ($query, $direction) {
                        return $query->orderBy('user.name', $direction);
                    })
                    ->getStateUsing(fn ($record) => isset($record->createur) ? $record->createur?->name . '  ' . $record->createur?->prenom : 'non spécifié'),
                Tables\Columns\TextColumn::make('assignee.name')
                    ->label('Assigné à')
                    ->sortable(query: function ($query, $direction) {
                        return $query->orderBy('user.name', $direction);
                    })
                    ->getStateUsing(fn ($record) => isset($record->assignee) ? $record->assignee?->name . '  ' . $record->assignee?->prenom : 'non spécifié'),

                Tables\Columns\TextColumn::make('priorite')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'critique' => 'danger',
                        'haute' => 'warning',
                        'moyenne' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'nouveau' => 'gray',
                        'attribue' => 'info',
                        'en_cours' => 'warning',
                        'cloture' => 'success',
                        default => 'danger',
                    })
                    ->sortable(),

                // type de ticket
                Tables\Columns\TextColumn::make('type_ticket')
                    ->label('Type de ticket')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('statut')
                    ->options([
                        'nouveau' => 'Nouveau',
                        'attribue' => 'Attribué',
                        'en_cours' => 'En cours',
                        'cloture' => 'Clôturé',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
            'show' => Pages\ShowTicket::route('/{record}'),
        ];
    }
}
