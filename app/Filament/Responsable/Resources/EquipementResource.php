<?php

namespace App\Filament\Responsable\Resources;

use App\Filament\Responsable\Resources\EquipementResource\Pages;
use App\Models\Equipement;
use App\Models\Bloc; // Ajout pour la relation
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EquipementResource extends Resource
{
    protected static ?string $model = Equipement::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube'; // Icône modifiée ici
    protected static ?string $navigationGroup = 'Gestion des équipements';
    protected static ?string $navigationLabel = 'Équipements';
    protected static ?string $slug = 'equipements';

    public static function form(Form $form): Form
    {
        return $form->schema([
            // récupére tous les blocs et permet au l'utilisatuer de rechercher par son nom il faut mettre a jour bloc_id dans la table equipements
            Forms\Components\Select::make('bloc_id')
                ->relationship('bloc', 'nom_bloc')
                ->required()
                ->searchable()
                ->preload()
                ->label('Bloc'),


            Forms\Components\TextInput::make('designation')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('marque')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('modele')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('numero_serie')
                ->required()
                ->maxLength(255),

            Forms\Components\DatePicker::make('date_acquisition')
                ->required(),

            Forms\Components\DatePicker::make('date_mise_en_service')
                ->required(),

            Forms\Components\Select::make('etat')
                ->options([
                    'bon' => 'Bon',
                    'acceptable' => 'Acceptable',
                    'mauvais' => 'Mauvais',
                    'hors_service' => 'Hors service',
                ])
                ->required(),

            Forms\Components\TextInput::make('fournisseur')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('contact_fournisseur')
                ->required()
                ->maxLength(255),

            Forms\Components\Select::make('type_equipement')
                ->options([
                    'medical' => 'Médical',
                    'informatique' => 'Informatique',
                    'technique' => 'Technique',
                    'autre' => 'Autre',
                ])
                ->required(),

            // la date fin du garantie doit être supérieur à la date d'acquisition par au moios un mois
            // min date doit etres un mois après la date d'acquisition
            Forms\Components\DatePicker::make('date_fin_garantie')
                ->required()
                ->after('date_acquisition')
                ->minDate(function (Forms\Get $get) {
                    $dateAcquisition = $get('date_acquisition');
                    if (!$dateAcquisition) {
                        return null; // Retourne null si aucune date d'acquisition n'est définie
                    }
                    return \Carbon\Carbon::parse($dateAcquisition)->addMonth();
                }),

            Forms\Components\Toggle::make('sous_contrat')
                ->required()
                ->reactive(),

            Forms\Components\TextInput::make('type_contrat')
                ->maxLength(255)
                ->hidden(fn (Forms\Get $get) => !$get('sous_contrat')),

            Forms\Components\TextInput::make('numero_contrat')
                ->maxLength(255)
                ->hidden(fn (Forms\Get $get) => !$get('sous_contrat')),


            Forms\Components\Select::make('criticite')
                ->options([
                    'haute' => 'Haute',
                    'moyenne' => 'Moyenne',
                    'basse' => 'Basse',
                ])
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('bloc.nom') // Relation vers le Bloc
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('designation')
                    ->searchable(),

                Tables\Columns\TextColumn::make('marque')
                    ->searchable(),

                Tables\Columns\TextColumn::make('modele')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('numero_serie')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('date_acquisition')
                    ->date()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('date_mise_en_service')
                    ->date()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('etat')
                    ->badge()
                    ->color(function ($state) {
                        return match ($state) {
                            'bon' => 'success',
                            'acceptable' => 'warning',
                            'mauvais' => 'danger',
                            'hors_service' => 'danger',
                            default => 'gray',
                        };
                    }),

                Tables\Columns\TextColumn::make('fournisseur')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('type_equipement')
                    ->searchable(),

                Tables\Columns\TextColumn::make('date_fin_garantie')
                    ->date()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('sous_contrat')
                    ->boolean(),

                Tables\Columns\TextColumn::make('criticite')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'haute' => 'danger',
                        'moyenne' => 'warning',
                        'basse' => 'success',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('etat')
                    ->options([
                        'bon' => 'Bon',
                        'acceptable' => 'Acceptable',
                        'mauvais' => 'Mauvais',
                        'hors_service' => 'Hors service',
                    ]),
                Tables\Filters\SelectFilter::make('type_equipement')
                    ->options([
                        'medical' => 'Médical',
                        'informatique' => 'Informatique',
                        'technique' => 'Technique',
                        'autre' => 'Autre',
                    ]),
                Tables\Filters\Filter::make('garantie_active')
                    ->query(fn ($query) => $query->where('date_fin_garantie', '>=', now())),
                Tables\Filters\SelectFilter::make('criticite')
                    ->options([
                        'haute' => 'Haute',
                        'moyenne' => 'Moyenne',
                        'basse' => 'Basse',
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
                    Tables\Actions\BulkAction::make('exporterPDF')
                        ->label('Exporter PDF')
                        ->icon('heroicon-o-document-arrow-down')
                        ->action(function ($records) {
                            // Logique d'export PDF
                        })
                ]),
            ])
            ->defaultSort('date_acquisition', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            // Ici, vous pouvez ajouter vos RelationManagers
            // Ex: RelationManagers\MaintenancesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEquipements::route('/'),
            'create' => Pages\CreateEquipement::route('/create'),
            'edit' => Pages\EditEquipement::route('/{record}/edit'),
            'view' => Pages\ViewEquipement::route('/{record}'),
        ];
    }
}
