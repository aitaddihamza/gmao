<?php

namespace App\Filament\SharedResources\TypeEquipement;

use App\Filament\SharedResources\TypeEquipement\TypeEquipementResource\Pages;
use App\Models\TypeEquipement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TypeEquipementResource extends Resource
{
    protected static ?string $model = TypeEquipement::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Gestion des équipements';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Types d\'équipements';
    protected static ?string $slug = 'types-equipements';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nom')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('nom')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('equipements_count')
                    ->counts('equipements')
                    ->label('Nombre d\'équipements'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date de création')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
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
            'index' => Pages\ListTypeEquipements::route('/'),
            'create' => Pages\CreateTypeEquipement::route('/create'),
            'edit' => Pages\EditTypeEquipement::route('/{record}/edit'),
            'view' => Pages\ViewTypeEquipement::route('/{record}'),
        ];
    }
}

