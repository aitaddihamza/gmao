<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables\Filters\SelectFilter;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    // change the title to "Utilisateurs"
    public static function getPluralModelLabel(): string
    {
        return 'Utilisateurs';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nom')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('prenom')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('telephone')
                    ->tel()
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\Select::make('role')
                    ->options([
                        'technicien' => 'Technicien',
                        'ingenieur' => 'Ingénieur',
                        'chef' => 'Directeur',
                        'majeur' => 'Majeur',
                        'responsable' => 'Responsable',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->label(fn (string $operation): string => $operation === 'create' ? 'Mot de passe' : 'Nouveau mot de passe')
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                    ->maxLength(255)
,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->where('role', '!=', 'admin'))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable(),
                Tables\Columns\TextColumn::make('prenom')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telephone'),
                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'technicien' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('email'),
            ])
            ->filters([
            SelectFilter::make('role')
                    ->label('Rôle')
                    ->options([
                        'technicien' => 'Technicien',
                        'ingenieur' => 'Ingénieur',
                        'chef' => 'Directeur',
                        'majeur' => 'Majeur',
                        'responsable' => 'Responsable',
                    ])
                    ->attribute('role'),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
