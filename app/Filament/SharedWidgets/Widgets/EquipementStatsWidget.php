<?php

namespace App\Filament\SharedWidgets\Widgets;

use App\Models\Equipement;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class EquipementStatsWidget extends BaseWidget
{
    public ?Equipement $equipement = null;
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        if (!$this->equipement) {
            return [];
        }

        $totalPannes = $this->equipement->mainteanceCorrectives->count();
        $ticketsCorrectifs = $this->equipement->mainteanceCorrectives;

        // Calcul de la moyenne des temps d'arrêt (en heures)
        $moyenneTempsArret = $ticketsCorrectifs->avg('temps_arret');
        $tempsArretFormatted = $moyenneTempsArret ? number_format($moyenneTempsArret, 1) . ' h' : 'N/A';

        // Temps d'arrêt total
        $totalTempsArret = $ticketsCorrectifs->sum('temps_arret');
        $totalTempsArretFormatted = $totalTempsArret ? number_format($totalTempsArret, 1) . ' h' : 'N/A';

        // Nombre de pannes cette année
        $pannesCetteAnnee = $this->equipement->mainteanceCorrectives()
            ->whereYear('created_at', now()->year)
            ->count();

        // Temps moyen entre pannes (MTBF - Mean Time Between Failures)
        $mtbf = $this->calculateMTBF();
        $mtbfFormatted = $mtbf ? $mtbf . ' jours' : 'N/A';

        // temps moyen de réparation (MTTR - Mean Time To Repair)
        $mttrFormatted = $this->calculerMTTR();
        $mttrFormatted = $mttrFormatted ? $mttrFormatted . ' h' : 'N/A';


        // MTTR Temps moyen de réparation
        $mttr = $this->calculerMTTR();

        // Tickets ouverts
        $ticketsOuverts = $this->equipement->tickets()
            ->where('statut', '!=', 'cloture')
            ->count();

        return [
            Stat::make('Total des pannes', $totalPannes)
                ->description('Historique complet')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color('danger'),

            Stat::make('Moyenne temps d\'arrêt', $tempsArretFormatted)
                ->description('Par incident')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Temps d\'arrêt total', $totalTempsArretFormatted)
                ->description('Temps cumulé')
                ->descriptionIcon('heroicon-o-clock')
                ->color('danger'),

            Stat::make('MTTR', $mttrFormatted)
                ->description('Temps moyen de réparation')
                ->descriptionIcon('heroicon-o-arrow-path')
                ->color('success'),

            Stat::make('MTBF', $mtbfFormatted)
                ->description('Temps moyen entre pannes')
                ->descriptionIcon('heroicon-o-arrow-path')
                ->color('success'),

            Stat::make('Tickets ouverts', $ticketsOuverts)
                ->description('En cours')
                ->descriptionIcon('heroicon-o-inbox')
                ->color($ticketsOuverts > 0 ? 'danger' : 'success'),
        ];
    }

    protected function calculateMTBF(): ?string
    {
        $tickets = $this->equipement->mainteanceCorrectives()
            ->orderBy('created_at')
            ->get();

        if ($tickets->count() < 2) {
            return null;
        }

        $firstDate = $tickets->first()->created_at;
        $lastDate = $tickets->last()->created_at;

        $totalDays = $firstDate->diffInDays($lastDate);
        $numberOfIntervals = $tickets->count() - 1;

        $mtbf = $totalDays / $numberOfIntervals;

        return number_format($mtbf, 1);
    }

    public static function canView(): bool
    {
        return true;
    }

    protected function calculerMTTR(): ?string
    {
        $tickets = $this->equipement->mainteanceCorrectives()
                                    ->get();

        if ($tickets->count() < 2) {
            return null;
        }

        // temps d'arrêt total
        $totalTempsArret = $tickets->sum('temps_arret');
        // nombre de pannes
        $nombreDePannes = $tickets->count();

        // MTTR = temps d'arrêt total / nombre de pannes

        $mttr = $totalTempsArret / $nombreDePannes;
        return number_format($mttr, 1);

    }

}
