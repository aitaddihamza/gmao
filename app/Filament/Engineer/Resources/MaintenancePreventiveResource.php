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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // choisir un équipement selon son nom
                // le champ est un select avec les options(noms des équipements) et la possibilité de rechercher par son nom
                Forms\Components\Select::make('equipement_id')
                    ->relationship('equipement', 'designation')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->reactive(),
                // ignore les utilisatuers qui ont le rôle (admin, ou responsable)
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->options(function (callable $get) {
                        return \App\Models\User::whereNotIn('role', ['admin', 'responsable'])
                            ->pluck('name', 'id');
                    }),
                Forms\Components\DatePicker::make('date_planifiee')
                    ->required(),
                Forms\Components\DatePicker::make('date_reelle'),
                Forms\Components\TextInput::make('statut')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('periodicite_jours')
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('remarques')
                    ->columnSpanFull(),
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
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
