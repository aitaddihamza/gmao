<?php

namespace App\Filament\Engineer\Resources;

use App\Filament\Engineer\Resources\MaintenancePreventiveResource\Pages;
use App\Filament\Engineer\Resources\MaintenancePreventiveResource\RelationManagers;
use App\Models\MaintenancePreventive;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MaintenancePreventiveResource extends Resource
{
    protected static ?string $model = MaintenancePreventive::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days'; // 📅 Pour la planification


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // une selection pour choisir l'équipement par nom
                Forms\Components\Select::make('equipement_id')
                    ->relationship('equipement', 'designation')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Équipement concerné')
                    ->default(fn ($record) => $record?->equipement_id), // Afficher la valeur existante
                // une selection pour choisir l'utilisateur par nom qui doit avoir un role de technicien
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Technicien responsable')
                    ->options(function () {
                        return \App\Models\User::where('role', 'technicien')
                            ->pluck('name', 'id'); // Filter users by role 'technicien'
                    })
                    ->default(fn ($record) => $record?->user_id), // Afficher la valeur existante

                // la date planifié doit être après la date actuel
                Forms\Components\DatePicker::make('date_planifiee')
                    ->required()
                    ->minDate(now())
                    ->label('Date planifiée')
                    ->default(fn ($record) => $record?->date_planifiee), // Afficher la valeur existante
                Forms\Components\DatePicker::make('date_reelle')
                    ->minDate(now())
                    ->default(fn ($record) => $record?->date_reelle), // Afficher la valeur existante
                // en cas d'edit afficher l'ancinne valeur
                Forms\Components\Select::make('statut')
                    ->required()
                    ->options([
                        'planifiee' => 'Planifiée',
                        'en_attente' => 'En attente',
                        'en_cours' => 'En cours',
                        'terminee' => 'Terminée',
                        'reportee' => 'Reportée',
                        'annulee' => 'Annulée',
                    ])
                    ->default(fn ($record) => $record?->statut), // Afficher la valeur existante
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull()
                    ->default(fn ($record) => $record?->description), // Afficher la valeur existante
                Forms\Components\TextInput::make('periodicite_jours')
                    ->required()
                    ->numeric()
                    ->default(fn ($record) => $record?->periodicite_jours), // Afficher la valeur existante
                Forms\Components\Textarea::make('remarques')
                    ->columnSpanFull()
                    ->default(fn ($record) => $record?->remarques), // Afficher la valeur existante
                // s'il a utilisé des pièces, il faut les ajouter et chacune son quantité
                // dans le cas d'edit afficher les pièces déjà utilisées
                Forms\Components\Repeater::make('pieces_utilisees')
                    ->schema([
                        Forms\Components\Select::make('piece_id')
                            ->options(\App\Models\Piece::pluck('designation', 'id'))
                            ->label('Pièce')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('quantite_utilisee')
                            ->required()
                            ->numeric(),
                    ])
                    ->columns(2)
                    ->createItemButtonLabel('Ajouter une pièce')
                    // Fix the default function to properly handle null records
                    ->default(function ($get, $record) {
                        if (!$record) {
                            return [];
                        }

                        return $record->pieces->map(function ($piece) {
                            return [
                                'piece_id' => $piece->id,
                                'quantite_utilisee' => $piece->pivot->quantite_utilisee,
                            ];
                        })->toArray();
                    })
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
               ->columns([
                   // Change this line to show equipment name instead of ID
                   Tables\Columns\TextColumn::make('equipement.designation')
                       ->label('Équipement')
                       ->sortable()
                       ->searchable(),
                   // Change this to show user name instead of ID
                   Tables\Columns\TextColumn::make('user.name')
                       ->label('Technicien')
                       ->sortable()
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
                   // Optimize pieces display
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
                   ->filters([
                       //
            ])
                   ->actions([
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
        ];
    }
}
