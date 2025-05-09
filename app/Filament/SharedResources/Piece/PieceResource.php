<?php

namespace App\Filament\SharedResources\Piece;

use App\Filament\SharedResources\Piece\PieceResource\Pages;
use App\Models\Piece;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

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
                    ->numeric(),

                Forms\Components\FileUpload::make('manuel_path')
                    ->label('Manuel d\'utilisation')
                    ->directory('manuels-pieces')
                    ->acceptedFileTypes(['application/pdf'])
                    ->downloadable()
                    ->openable()
                    ->columnSpanFull(),
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
            'index' => Pages\ListPieces::route('/'),
            'create' => Pages\CreatePiece::route('/create'),
            'edit' => Pages\EditPiece::route('/{record}/edit'),
            'view' => Pages\ViewPiece::route('/{record}'),
        ];
    }
}
