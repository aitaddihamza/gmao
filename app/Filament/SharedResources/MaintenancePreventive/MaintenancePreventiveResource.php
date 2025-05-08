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
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use App\Services\ReportService;
use Illuminate\Support\Facades\Storage;

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
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->designation . ' - ' . $record->modele . ' - ' . $record->marque . ' - ' . $record->bloc->localisation),
                Forms\Components\Hidden::make('user_createur_id')
                    ->default(fn () => auth()->id())
                    ->required(),
                    Forms\Components\Select::make('user_assignee_id')
                    ->relationship('assignee', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Assigné à')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name . ' ' . $record->prenom . ' - ' . $record->role),
                Forms\Components\DateTimePicker::make('date_debut')
                    ->required()
                    ->minDate(now())
                    ->label('Date de début'),
                Forms\Components\DateTimePicker::make('date_fin')
                    ->minDate(now())
                    ->label('Date de fin'),
                Forms\Components\Select::make('statut')
                    ->required()
                    ->reactive()
                    ->options([
                        'planifiee' => 'Planifiée',
                        'en_attente' => 'En attente',
                        'en_cours' => 'En cours',
                        'termine' => 'Terminée',
                        'reportee' => 'Reportée',
                        'annulee' => 'Annulée',
                    ])
                    ->default(fn ($record) => $record?->statut ?? 'planifiee')
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if ($state === 'termine') {
                            $set('date_realisation', now());
                        }
                    }),
                    Forms\Components\Select::make('equipement_etat')
                    ->hidden(fn (Forms\Get $get) => $get('statut') != 'termine' && $get('statut') != 'annulee' && $get('statut') != 'reportee')
                    ->required()
                    ->searchable()
                    ->options([
                        'bon' => 'Bon',
                        'acceptable' => 'Acceptable',
                        'mauvais' => 'Mauvais',
                        'hors_service' => 'Hors service',
                    ])
                    ->label('État de l\'equipemnet'),
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
                    ->disableLabel(false),
                Section::make('Rapport d\'intervention')
                    ->schema([
                        Select::make('rapport_type')
                            ->label('Type de rapport')
                            ->options([
                                'manuel' => 'Upload manuel',
                                'auto' => 'Génération automatique',
                            ])
                            ->default('manuel')
                            ->visible(fn (Forms\Get $get) => $get('statut') === 'termine')
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set, $record) {
                                if ($record && $record->rapport_path) {
                                    $oldPath = $record->rapport_path;
                                    if (Storage::disk('public')->exists($oldPath)) {
                                        Storage::disk('public')->delete($oldPath);
                                    }
                                }
                                $set('rapport_path', null);
                            }),

                        FileUpload::make('rapport_path')
                            ->label('Rapport')
                            ->directory('reports/maintenance')
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                            ->maxSize(5120)
                            ->visible(fn (Forms\Get $get) => $get('statut') === 'termine' && $get('rapport_type') === 'manuel')
                            ->required(fn (Forms\Get $get) => $get('statut') === 'termine' && $get('rapport_type') === 'manuel'),


                        Textarea::make('actions_realisees')
                            ->label('Actions réalisées')
                            ->rows(3)
                            ->visible(fn (Forms\Get $get) => $get('statut') === 'termine'),


                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('generateReport')
                                ->label('Générer le rapport')
                                ->icon('heroicon-o-document-text')
                                ->visible(fn (Forms\Get $get) => $get('statut') === 'termine' && $get('rapport_type') === 'auto')
                                ->action(function ($record) {
                                    $reportService = new ReportService();
                                    $path = $reportService->generateMaintenancePreventiveReport($record, $record->equipement);

                                    $record->update([
                                        'rapport_path' => $path,
                                        'rapport_type' => 'auto'
                                    ]);

                                    Notification::make()
                                        ->title('Rapport généré avec succès')
                                        ->success()
                                        ->send();
                                })
                        ]),
                    ])
                    ->collapsible()
                    ->visible(fn (Forms\Get $get) => $get('statut') === 'termine'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Action::make('calendrier')
                    ->label('Calendrier')
                    ->url(fn () => route('filament.'. auth()->user()->role .'.pages.calendar'))
                    ->icon('heroicon-o-calendar')
                    ->color('yellow'),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('equipement.designation')
                    ->label('Équipement')
                    ->sortable(query: function ($query, $direction) {
                        return $query->orderBy('equipement.designation', $direction);
                    })
                    ->searchable()
                    ->getStateUsing(fn ($record) => $record->equipement?->designation . ' - ' . $record->equipement?->modele),
                Tables\Columns\TextColumn::make('createur.name')
                    ->label('Créé par')
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->createur?->name . ' ' . $record->createur?->prenom)
                    ->searchable(),
                Tables\Columns\TextColumn::make('assignee.name')
                    ->label('Assigné à')
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->assignee?->name . ' ' . $record->assignee?->prenom)
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_debut')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_fin')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Non définie'),
                Tables\Columns\TextColumn::make('statut')
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'planifiee' => 'info',
                        'en_attente' => 'warning',
                        'en_cours' => 'primary',
                        'termine' => 'success',
                        'reportee' => 'gray',
                        'annulee' => 'danger',
                    }),
                Tables\Columns\IconColumn::make('type_externe')
                    ->boolean(),
                Tables\Columns\TextColumn::make('fournisseur'),
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
            ->defaultSort('date_debut', 'desc')
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
            'view' => Pages\ViewMaintenancePreventive::route('/{record}'),
            'edit' => Pages\EditMaintenancePreventive::route('/{record}/edit'),
        ];
    }
}
