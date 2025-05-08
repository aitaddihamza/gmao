<?php

namespace App\Filament\SharedResources\MaintenanceCorrective;

use App\Filament\SharedResources\MaintenanceCorrective\MaintenanceCorrectiveResource\Pages;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class MaintenanceCorrectiveResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog';
    // sous le groupe de mainteancne
    protected static ?string $navigationGroup = 'Maintenance';

    // changer le label ou bien le titre par maintenance corrective
    protected static ?string $label = 'Maintenance corrective';


    public static function getModel(): string
    {
        return Ticket::class;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('type_ticket', 'correctif');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('equipement.designation')
                    ->sortable(query: function ($query, $direction) {
                        return $query->orderBy('equipement.designation', $direction);
                    })
                    ->searchable()
                    ->getStateUsing(fn ($record) => $record->equipement?->designation . ' - ' . $record->equipement?->modele)
                    ->label('Équipement concerné'),

                Tables\Columns\TextColumn::make('createur.name')
                    ->label('Créé par')
                    ->sortable(query: function ($query, $direction) {
                        return $query->orderBy('user.name', $direction);
                    })
                    ->getStateUsing(fn ($record) => isset($record->createur) ? $record->createur?->name . '  ' . $record->createur?->prenom : 'non spécifié'),

                Tables\Columns\TextColumn::make('assignee.name')
                    ->label('Assigné à')
                    ->sortable(query: function ($query, $direction) {
                        return $query->orderBy('user.name', $direction);
                    })
                    ->getStateUsing(fn ($record) => isset($record->assignee) ? $record->assignee?->name . '  ' . $record->assignee?->prenom : 'non spécifié'),

                // type externe ou pas
                Tables\Columns\IconColumn::make('type_externe')
                    ->boolean(),
                Tables\Columns\TextColumn::make('fournisseur') ,
                // la date de création
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date de création')
                    ->dateTime('d/m/Y H:i')
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

                Tables\Columns\TextColumn::make('date_resolution')
                    ->label('Date de résolution')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('temps_arret')
                    ->label('Temps d\'arrêt')
                    ->getStateUsing(function ($record) {
                        if ($record->date_intervention && $record->date_resolution) {
                            $intervention = Carbon::parse($record->date_intervention);
                            $resolution = Carbon::parse($record->date_resolution);
                            return $intervention->diffInHours($resolution) . ' heures';
                        }
                        return 'Non calculé';
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
                Tables\Filters\SelectFilter::make('priorite')
                    ->options([
                        'critique' => 'Critique',
                        'haute' => 'Haute',
                        'moyenne' => 'Moyenne',
                        'basse' => 'Basse',
                    ]),
                Tables\Filters\SelectFilter::make('type_externe')
                    ->options([
                        'interne' => 'Interne',
                        'externe' => 'Externe',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn ($record) => route(auth()->user()->role == 'ingenieur' ? 'filament.engineer.resources.tickets.show' : 'filament.' . auth()->user()->role . '.resources.tickets.show', $record->id)),
                Tables\Actions\EditAction::make()
                    ->url(fn ($record) => route(auth()->user()->role == 'ingenieur' ? 'filament.engineer.resources.tickets.edit' : 'filament.' . auth()->user()->role . '.resources.tickets.edit', $record->id)),
                Tables\Actions\DeleteAction::make()
                    ->action(function ($record) {
                        $record->delete();
                        return redirect()->route(auth()->user()->role == 'ingenieur' ? 'filament.engineer.resources.tickets.index' : 'filament.' . auth()->user()->role . '.resources.tickets.index');
                    }),
            ])
            ->defaultSort('updated_at', 'desc')
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
            'index' => Pages\ListMaintenanceCorrectives::route('/'),
            'create' => \App\Filament\SharedResources\Ticket\TicketResource\Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditMaintenanceCorrective::route('/{record}/edit'),
        ];
    }
}
