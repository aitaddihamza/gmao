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
                    ->label('Équipement concerné'),
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
                    }),

                // la date planifié doit être après la date actuel
                Forms\Components\DatePicker::make('date_planifiee')
                    ->required()
                    ->minDate(now())
                    ->label('Date planifiée'),
                Forms\Components\DatePicker::make('date_reelle')
                    ->minDate(now()),
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
                    ->default('planifiee')
                    ->preload(),  
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('periodicite_jours')
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('remarques')
                    ->columnSpanFull(),
                // s'il a utilisé des pièces, il faut les ajouter et chacune son quantité
                // dans le cas d'edit afficher les pièces déjà utilisées
                Forms\Components\Repeater::make('pieces_utilisees') // Utilisez un nom différent pour éviter les conflits
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
                    // Spécifiez explicitement le nom de la relation ici
                // ->relationship('pieces')
                ->createItemButtonLabel('Ajouter une pièce')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('equipement_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_planifiee')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_reelle')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('statut')
                    ->searchable(),
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
                // afficher les pieces utilisées avec la quantité
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
