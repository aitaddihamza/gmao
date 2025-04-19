<?php

namespace App\Filament\Responsable\Resources;

use App\Filament\Responsable\Resources\PieceResource\Pages;
use App\Filament\Responsable\Resources\PieceResource\RelationManagers;
use App\Models\Piece;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PieceResource extends Resource
{
    protected static ?string $model = Piece::class;

    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';

    // remplacer "piece" de navigation par "stock"
    protected static ?string $navigationLabel = 'Stock';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('designation')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('reference')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('quantite_stock')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('fournisseur')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('prix_unitaire')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('designation')
                    ->searchable(),
                Tables\Columns\TextColumn::make('reference')
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantite_stock')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fournisseur')
                    ->searchable(),
                Tables\Columns\TextColumn::make('prix_unitaire')
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
            'index' => Pages\ListPieces::route('/'),
            'create' => Pages\CreatePiece::route('/create'),
            'edit' => Pages\EditPiece::route('/{record}/edit'),
        ];
    }
}
