<?php

namespace App\Filament\SharedWidgets\Widgets;

use App\Models\Ticket;
use App\Models\Equipement;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class EquipementChartWidget extends ChartWidget
{
    public ?Equipement $equipement = null;
    protected static ?string $heading = 'Historique des pannes et temps d\'arrêt';
    protected static ?string $pollingInterval = '30s';
    protected int | string | array $columnSpan = 'full';


    protected function getData(): array
    {
        if (!$this->equipement) {
            return [];
        }

        // Récupérer les données des 12 derniers mois
        $startDate = Carbon::now()->subMonths(11)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        // Requête pour les pannes groupées par mois
        $pannesData = Ticket::query()
            ->where('equipement_id', $this->equipement->id)
            ->where('type_ticket', 'correctif')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Requête pour le temps d'arrêt cumulé par mois
        $tempsArretData = Ticket::query()
            ->where('equipement_id', $this->equipement->id)
            ->where('type_ticket', 'correctif')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(temps_arret) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Générer les labels pour tous les mois (même sans données)
        $labels = [];
        $current = $startDate->copy();
        while ($current <= $endDate) {
            $labels[] = $current->format('M Y');
            $current->addMonth();
        }

        // Aligner les données avec les labels
        $pannesCounts = [];
        $tempsArretTotals = [];
        foreach ($labels as $label) {
            $key = Carbon::createFromFormat('M Y', $label)->format('Y-m');
            $pannesCounts[] = $pannesData[$key] ?? 0;
            $tempsArretTotals[] = $tempsArretData[$key] ?? 0;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Nombre de pannes',
                    'data' => $pannesCounts,
                    'backgroundColor' => '#ef4444', // Rouge
                    'borderColor' => '#dc2626',
                    'type' => 'bar',
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Temps d\'arrêt (heures)',
                    'data' => $tempsArretTotals,
                    'borderColor' => '#3b82f6', // Bleu
                    'backgroundColor' => 'transparent',
                    'type' => 'line',
                    'yAxisID' => 'y1',
                    'tension' => 0.3,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // Type de base, mais mixé avec 'line' dans les datasets
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Nombre de pannes',
                    ],
                    'position' => 'left',
                ],
                'y1' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Temps d\'arrêt (h)',
                    ],
                    'position' => 'right',
                    'grid' => [
                        'drawOnChartArea' => false, // Évite la superposition avec l'axe Y gauche
                    ],
                ],
            ],
            'interaction' => [
                'mode' => 'index', // Affiche les infos des deux datasets au survol
            ],
        ];
    }

    public static function canView(): bool
    {
        return true;
    }


}
