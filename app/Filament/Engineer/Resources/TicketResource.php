<?php

namespace App\Filament\Engineer\Resources;

use App\Filament\Engineer\Resources\TicketResource\Pages;
use App\Filament\Engineer\Resources\TicketResource\RelationManagers;
use App\Models\Ticket;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Équipement concerné'),

                        Forms\Components\Select::make('type_ticket')
                            ->options([
                                'correctif' => 'Maintenance corrective',
                                'preventif' => 'Maintenance préventive',
                                'installation' => 'Installation',
                                'formation' => 'Formation',
                                'autre' => 'Autre',
                            ])
                            ->required()
                            ->default('correctif'),

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

                        Forms\Components\Select::make('priorite')
                            ->options([
                                'critique' => 'Critique - Urgent',
                                'haute' => 'Haute',
                                'moyenne' => 'Moyenne',
                                'basse' => 'Basse',
                            ])
                            ->required()
                            ->default('moyenne'),
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
                            ->visibility('public') // Ajout important
                            ->preserveFilenames() // Optionnel - conserve les noms originaux
                            ->imagePreviewHeight('250') // Meilleur aperçu
                            ->openable() // Permet d'ouvrir les fichiers
                            ->downloadable() // Permet de les télécharger
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Assignation')
                    ->schema([
                    Forms\Components\Hidden::make('user_createur_id')
                       ->default(fn () => auth()->id())
                       ->required(),

                        Forms\Components\Select::make('user_assignee_id')
                            ->label('Technicien assigné')
                            ->options(
                                User::whereNotIn('role', ['admin', 'responsable'])
                                    ->get()
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->required()
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
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('assignee.name')
                    ->label('Assigné à')
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

                Tables\Columns\TextColumn::make('priorite')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'critique' => 'danger',
                        'haute' => 'warning',
                        'moyenne' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),
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
        ];
    }
}
