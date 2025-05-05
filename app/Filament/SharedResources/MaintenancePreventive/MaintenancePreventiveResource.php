<?php

namespace App\Filament\SharedResources\MaintenancePreventive;

use App\Filament\SharedResources\MaintenancePreventive\MaintenancePreventiveResource\Pages;
use App\Models\MaintenancePreventive;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;

class MaintenancePreventiveResource extends Resource
{
    protected static ?string $model = MaintenancePreventive::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench';
    // sous groupe de maintenance
    protected static ?string $navigationGroup = 'Maintenance';
    // sous groupe maintenance préventive
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Maintenance préventive';
    protected static ?string $slug = 'maintenance-preventive';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('equipement_id')
                    ->relationship('equipement', 'designation')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Équipement concerné')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->designation . ' - ' . $record->modele),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Technicien responsable')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name . '  ' . $record->prenom),
                Forms\Components\DatePicker::make('date_planifiee')
                    ->required()
                    ->minDate(now())
                    ->label('Date planifiée'),
                Forms\Components\DatePicker::make('date_reelle')
                    ->minDate(now()),
                Forms\Components\Select::make('statut')
                    ->required()
                    ->reactive()
                    ->options([
                        'planifiee' => 'Planifiée',
                        'en_attente' => 'En attente',
                        'en_cours' => 'En cours',
                        'terminee' => 'Terminée',
                        'reportee' => 'Reportée',
                        'annulee' => 'Annulée',
                    ]),
                Forms\Components\Select::make('equipement_etat')
                    ->label('Etat de l\'equipement')
                    ->hidden(fn (Forms\Get $get) => in_array($get('statut'), ['planifiee', 'en_attente', 'en_cours']))
                    ->options([
                        'bon' => 'Bon',
                        'acceptable' => 'Acceptable',
                        'mauvais' => 'Mauvais',
                        'hors_service' => 'Hors service',
                    ]),
                Forms\Components\Toggle::make('type_externe')
                    ->label('Type externe ?')
                    ->reactive(),
                Forms\Components\TextInput::make('fournisseur')
                    ->maxLength(255)
                    ->placeholder('Nom du fournisseur')
                    ->hidden(fn (Forms\Get $get) => !$get('type_externe')),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('periodicite_jours')
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('remarques')
                    ->columnSpanFull(),
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
            ]);
    }

    public static function table(Table $table): Table
    {

        return $table
                ->headerActions([
                    Action::make('calendrier')
                        ->label('Calendrier')
                        ->url(fn () => route('filament.engineer.pages.calendar'))
                        ->icon('heroicon-o-calendar') // optional icon
                        ->color('yellow'), // optional color
                ])
                ->columns([
                Tables\Columns\TextColumn::make('equipement.designation')
                    ->label('Équipement')
                    ->sortable(query: function ($query, $direction) {
                        return $query->orderBy('equipement.designation', $direction);
                    })
                    ->searchable()
                    ->getStateUsing(fn ($record) => $record->equipement?->designation . ' - ' . $record->equipement?->modele),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Technicien')
                    ->sortable()
                    ->sortable(query: function ($query, $direction) {
                        return $query->orderBy('user.name', $direction);
                    })
                    ->getStateUsing(fn ($record) => isset($record->user) ? $record->user?->name . '  ' . $record->user?->prenom : 'non spécifié')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_planifiee')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_reelle')
                    ->date()
                    ->sortable()
                    ->placeholder('Non définie'),
                Tables\Columns\TextColumn::make('statut')
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'planifiee' => 'info',
                        'en_attente' => 'warning',
                        'en_cours' => 'primary',
                        'terminee' => 'success',
                        'reportee' => 'gray',
                        'annulee' => 'danger',
                    }),
                Tables\Columns\IconColumn::make('type_externe')
                    ->boolean(),
                Tables\Columns\TextColumn::make('fournisseur'),
                Tables\Columns\TextColumn::make('periodicite_jours')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('pieces_count')
                    ->label('Pièces utilisées')
                    ->getStateUsing(function ($record) {
                        $piecesInfo = [];
                        foreach ($record->pieces as $piece) {
                            $piecesInfo[] = $piece->pivot->quantite_utilisee . ' x ' . $piece->designation;
                        }
                        return !empty($piecesInfo) ? implode(', ', $piecesInfo) : 'Aucune pièce';
                    }),
            ])
            ->defaultSort('date_planifiee', 'desc')
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMaintenancePreventives::route('/'),
            'create' => Pages\CreateMaintenancePreventive::route('/create'),
            'edit' => Pages\EditMaintenancePreventive::route('/{record}/edit'),
            'view' => Pages\ViewMaintenancePreventive::route('/{record}'),
        ];
    }
}
