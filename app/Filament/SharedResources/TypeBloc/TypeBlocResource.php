<?php

namespace App\Filament\SharedResources\TypeBloc;
    
use App\Filament\SharedResources\TypeBloc\TypeBlocResource\Pages;
use App\Models\TypeBloc;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TypeBlocResource extends Resource
{
    protected static ?string $model = TypeBloc::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Gestion des blocs';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Types de blocs';
    protected static ?string $slug = 'types-blocs';

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

                Tables\Columns\TextColumn::make('blocs_count')
                    ->counts('blocs')
                    ->label('Nombre de blocs'),

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
            'index' => Pages\ListTypeBlocs::route('/'),
            'create' => Pages\CreateTypeBloc::route('/create'),
            'edit' => Pages\EditTypeBloc::route('/{record}/edit'),
            'view' => Pages\ViewTypeBloc::route('/{record}'),
        ];
    }
}

