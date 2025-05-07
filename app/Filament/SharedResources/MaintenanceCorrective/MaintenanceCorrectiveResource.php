<?php

namespace App\Filament\SharedResources\MaintenanceCorrective;

use App\Filament\SharedResources\MaintenanceCorrective\MaintenanceCorrectiveResource\Pages;
use App\Models\MaintenanceCorrective;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Ticket;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class MaintenanceCorrectiveResource extends Resource
{
    protected static ?string $model = MaintenanceCorrective::class;

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
                    ->label('Équipement concerné')
                    ->url(fn ($record) => route('filament.engineer.resources.tickets.edit', $record->id)),

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

                Tables\Columns\TextColumn::make('date_attribution')
                    ->label('Date d\'attribution')
                    ->dateTime('d/m/Y H:i')
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
                    ->url(fn ($record) => route('filament.engineer.resources.tickets.edit', $record->id)),
                Tables\Actions\EditAction::make()
                    ->url(fn ($record) => route('filament.engineer.resources.tickets.edit', $record->id)),
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
            'index' => Pages\ListMaintenanceCorrectives::route('/'),
            'create' => \App\Filament\SharedResources\Ticket\TicketResource\Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditMaintenanceCorrective::route('/{record}/edit'),
        ];
    }
}
