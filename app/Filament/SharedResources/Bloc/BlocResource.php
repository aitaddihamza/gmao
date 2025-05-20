<?php

namespace App\Filament\SharedResources\Bloc;

use App\Filament\SharedResources\Bloc\BlocResource\Pages;
use App\Models\Bloc;
use App\Models\TypeBloc;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BlocResource extends Resource
{
    protected static ?string $model = Bloc::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'Gestion des blocs';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'services';
    protected static ?string $slug = 'blocs';
    protected static ?string $pluralLabel = 'Seicrves';

    protected static ?string $modelLabel = 'service';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nom_bloc')
                ->label('Nom du service')
                ->required()
                ->maxLength(255),

            Forms\Components\Select::make('type_bloc_id')
                ->relationship('typeBloc', 'nom')
                ->required()
                ->searchable()
                ->preload()
                ->label('type de service'),

            Forms\Components\Textarea::make('description')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('localisation')
                ->required()
                ->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('nom_bloc')
                    ->label('Nom de service')
                    ->searchable(),

                Tables\Columns\TextColumn::make('typeBloc.nom')
                    ->sortable()
                    ->searchable()
                    ->label('Type de service'),

                Tables\Columns\TextColumn::make('description')
                    ->searchable(),

                Tables\Columns\TextColumn::make('localisation')
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type_bloc_id')
                    ->relationship('typeBloc', 'nom')
                    ->label('Type de bloc'),
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
            'index' => Pages\ListBlocs::route('/'),
            'create' => Pages\CreateBloc::route('/create'),
            'edit' => Pages\EditBloc::route('/{record}/edit'),
            'view' => Pages\ViewBloc::route('/{record}'),
        ];
    }
}
