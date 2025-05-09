<?php

namespace App\Filament\SharedPages\Pages;

use Filament\Pages\Page;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Equipement;
use Carbon\Carbon;
use Filament\Tables\Actions\ViewAction;

class Classements extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static string $view = 'filament.shared.pages.classements';
    protected static ?string $navigationLabel = 'Classements';
    protected static ?string $navigationGroup = 'Gestion des équipements';
    protected static ?int $navigationSort = 4;

    protected function getTableQuery(): Builder
    {
        return Equipement::query()
            ->withCount(['mainteanceCorrectives as breakdowns_count'])
            ->withCount(['maintenancePreventives as mps']);
    }

    // ajoute l'action de voir les détails de l'équipement
    protected function getTableActions(): array
    {
        return [
            ViewAction::make()
                ->label('Voir les détails')
                ->url(fn (Equipement $record): string => VoirEquipement::getUrl(['id' => $record->id]))
                ->icon('heroicon-o-eye')
                ->color('primary')
                ->openUrlInNewTab(),
        ];
    }

    protected function getTableColumns(): array
    {
        return
             [

                 Tables\Columns\TextColumn::make('designation')
                     ->searchable(),

                 Tables\Columns\TextColumn::make('bloc.nom_bloc')
                     ->getStateUsing(fn ($record) => $record->bloc->nom_bloc. ' - ' . $record->bloc->typeBloc->nom)
                     ->searchable(),

                 Tables\Columns\TextColumn::make('modele')
                     ->searchable()
                     ->toggleable(),

                 Tables\Columns\TextColumn::make('bloc.localisation')
                     ->label('localisation')
                     ->searchable(),

                 Tables\Columns\TextColumn::make('marque')
                     ->searchable(),

                 Tables\Columns\TextColumn::make('numero_serie')
                     ->searchable()
                     ->toggleable(isToggledHiddenByDefault: true),

                 Tables\Columns\TextColumn::make('etat')
                     ->badge()
                     ->color(function ($state, $record) {
                         return match ($state) {
                             'bon', 'acceptable' => 'success',
                             'mauvais', 'hors_service' => 'danger',
                             default => 'gray',
                         };
                     }),

                 Tables\Columns\TextColumn::make('fournisseur')
                     ->searchable()
                     ->toggleable(isToggledHiddenByDefault: true),

                 Tables\Columns\TextColumn::make('typeEquipement.nom')
                     ->sortable()
                     ->searchable()
                     ->label('Type d\'équipement'),

                 Tables\Columns\TextColumn::make('criticite')
                     ->badge()
                     ->color(fn (string $state): string => match ($state) {
                         'haute' => 'danger',
                         'moyenne' => 'warning',
                         'basse' => 'success',
                         default => 'gray',
                     }),
                 // nombre des pannes
                 Tables\Columns\TextColumn::make('breakdowns_count')
                     ->label('Nombre de pannes')
                     ->sortable()
                     ->getStateUsing(function ($record) {
                         return $record->breakdowns_count;
                     })
                     ->color('danger')
                     ->icon('heroicon-o-exclamation-triangle')
                     ->tooltip('Nombre de pannes'),

                 // nombre des maintenances préventives
                 Tables\Columns\TextColumn::make('mps')
                     ->label('Maintenances préventives')
                     ->sortable()
                     ->getStateUsing(function ($record) {
                         return $record->mps;
                     })
                     ->color('success')
                     ->icon('heroicon-o-wrench')
                     ->tooltip('Nombre des Maintenances préventives'),

                 // date fin de garantie s'il est termine affiche en rouge
                 Tables\Columns\TextColumn::make('date_fin_garantie')
                     ->label('Date fin de garantie')
                     ->date()
                     ->color(function ($state) {
                         return $state < now() ? 'danger' : 'success';
                     })
                     ->tooltip('Date de fin de garantie'),

                 Tables\Columns\IconColumn::make('sous_contrat')
                     ->boolean(),

             ];

    }

    protected function getTableFilters(): array
    {
        return [
            Tables\Filters\SelectFilter::make('year')
                ->label('Année')
                ->options($this->getYears())
                ->query(function (Builder $query, array $data) { // Modifié ici
                    $year = $data['value'] ?? null;
                    if ($year && is_string($year) && trim($year) !== '') {
                        $query->whereHas('tickets', function ($subQuery) use ($year) {
                            $subQuery->whereYear('created_at', $year)
                                    ->where('type_ticket', 'correctif');
                        });
                    }
                }),
            Tables\Filters\SelectFilter::make('etat')
                ->options([
                    'bon' => 'Bon',
                    'acceptable' => 'Acceptable',
                    'mauvais' => 'Mauvais',
                    'hors_service' => 'Hors service',
                ]),
            Tables\Filters\SelectFilter::make('type_equipement_id')
                ->relationship('typeEquipement', 'nom')
                ->label('Type d\'équipement'),
            Tables\Filters\Filter::make('garantie_active')
                ->query(fn ($query) => $query->where('date_fin_garantie', '>=', now())),
            Tables\Filters\SelectFilter::make('criticite')
                ->options([
                    'haute' => 'Haute',
                    'moyenne' => 'Moyenne',
                    'basse' => 'Basse',
                ]),
            // ajoute sous contrat
            Tables\Filters\SelectFilter::make('sous_contrat')
                ->options([
                    '1' => 'Sous contrat',
                    '0' => 'Hors contrat',
                ])
                ->label('Sous contrat'),

        ];
    }
    private function getYears(): array
    {
        $startYear = now()->subYears(10)->year;
        $endYear = now()->year;
        return collect(range($endYear, $startYear))
            ->mapWithKeys(fn ($year) => [(string) $year => (string) $year])
            ->toArray();
    }
}
