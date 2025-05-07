<?php

namespace App\Filament\SharedResources\Equipement;

use App\Filament\SharedResources\Equipement\EquipementResource\Pages;
use App\Models\Equipement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Carbon\Carbon;

class EquipementResource extends Resource
{
    protected static ?string $model = Equipement::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Gestion des équipements';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Équipements';
    protected static ?string $slug = 'equipements';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('bloc_id')
                ->relationship('bloc', 'nom_bloc')
                ->getOptionLabelFromRecordUsing(fn ($record) => $record->nom_bloc . ' - ' . $record->typeBloc->nom . ' - ' . $record->localisation)
                ->required()
                ->searchable()
                ->preload()
                ->label('Bloc'),

            Forms\Components\Select::make('type_equipement_id')
                ->relationship('typeEquipement', 'nom')
                ->required()
                ->searchable()
                ->preload()
                ->label('Type d\'équipement'),

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

            Forms\Components\Select::make('criticite')
                ->options([
                    'haute' => 'Haute',
                    'moyenne' => 'Moyenne',
                    'basse' => 'Basse',
                ])
                ->required(),

            Forms\Components\DatePicker::make('date_fin_garantie')
                ->required()
                ->after('date_acquisition')
                ->minDate(function (Forms\Get $get) {
                    $dateAcquisition = $get('date_acquisition');
                    if (!$dateAcquisition) {
                        return null;
                    }
                    return \Carbon\Carbon::parse($dateAcquisition)->addMonth();
                })
                ->validationAttribute('date de fin de garantie')
                ->validationMessages([
                    'after' => 'La date de fin de garantie doit être postérieure à la date d\'acquisition',
                    'required' => 'La date de fin de garantie est requise',
                ]),

            Forms\Components\Toggle::make('sous_contrat')
                ->required()
                ->reactive(),

            Forms\Components\TextInput::make('type_contrat')
                ->maxLength(255)
                ->hidden(fn (Forms\Get $get) => !$get('sous_contrat')),

            Forms\Components\TextInput::make('numero_contrat')
                ->maxLength(255)
                ->hidden(fn (Forms\Get $get) => !$get('sous_contrat')),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('bloc.nom_bloc')
                    ->getStateUsing(fn ($record) => $record->bloc->nom_bloc. ' - ' . $record->bloc->typeBloc->nom)
                    ->searchable(),

                Tables\Columns\TextColumn::make('designation')
                    ->searchable(),

                Tables\Columns\TextColumn::make('modele')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('bloc.localisation')
                    ->label('localisation')
                    ->searchable(),

                Tables\Columns\TextColumn::make('marque')
                    ->searchable(),

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
                    ->color(function ($state, $record) {
                        // Récupérer les maintenances préventives liées
                        $maintenances = $record->maintenancePreventives;
                        // Date d'aujourd'hui
                        $today = Carbon::now()->toDateString();

                        foreach ($maintenances as $mp) {
                            if ($mp->date_planifiee && Carbon::parse($mp->date_planifiee)->toDateString() === $today) {
                                // Mettre à jour l'état uniquement s'il n'est pas déjà à 'hors_service'
                                if ($record->etat !== 'hors_service') {
                                    $record->update(['etat' => 'hors_service']);
                                }

                                $state = 'hors_service'; // Forcer l'affichage aussi
                                break;
                            }
                        }

                        return match ($state) {
                            'bon', 'acceptable' => 'success',
                            'mauvais', 'hors_service' => 'danger',
                            default => 'gray',
                        };
                    }),

                Tables\Columns\TextColumn::make('fournisseur')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('typeEquipement.nom')
                    ->sortable()
                    ->searchable()
                    ->label('Type d\'équipement'),

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
                Tables\Filters\SelectFilter::make('type_equipement_id')
                    ->relationship('typeEquipement', 'nom')
                    ->label('Type d\'équipement'),
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
            //
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
