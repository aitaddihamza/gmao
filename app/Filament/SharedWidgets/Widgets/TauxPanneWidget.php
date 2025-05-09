<?php

namespace App\Filament\SharedWidgets\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\TypeEquipement;
use App\Models\Ticket;

class TauxPanneWidget extends ChartWidget
{
    protected static ?string $heading = 'Répartition des taux de pannes par type d\'équipement';
    protected static ?string $pollingInterval = '30s';
    protected static ?string $maxHeight = '500px';
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        // Récupérer tous les types d'équipement
        $typesEquipement = TypeEquipement::withCount(['equipements'])
            ->get()
            ->filter(fn ($type) => $type->equipements_count > 0); // Filtrer les types avec au moins 1 équipement

        $labels = [];
        $tauxPannes = [];
        $couleurs = [];

        foreach ($typesEquipement as $type) {
            // Compter les tickets de type 'correctif' pour ce type d'équipement
            $pannesCount = Ticket::where('type_ticket', 'correctif')
                ->whereHas('equipement', function ($query) use ($type) {
                    $query->where('type_equipement_id', $type->id);
                })
                ->distinct('equipement_id') // Compter les équipements distincts
                ->count();

            // Calculer le taux de panne (nombre de pannes / nombre d'équipements * 100)
            $taux = $type->equipements_count > 0
                ? ($pannesCount / $type->equipements_count) * 100
                : 0;

            $labels[] = $type->nom;
            $tauxPannes[] = round($taux, 2); // Arrondir à 2 décimales
            $couleurs[] = $this->generateColor($type->nom); // Générer une couleur unique
        }

        return [
            'datasets' => [
                [
                    'label' => 'Taux de panne (%)',
                    'data' => $tauxPannes,
                    'backgroundColor' => $couleurs,
                    'borderColor' => '#9CA3AF',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // ou 'pie' pour un camembert
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Taux de panne (%)'
                    ]
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Types d\'équipement'
                    ]
                ]
            ],
            'plugins' => [
                'legend' => [
                    'display' => false, // Masquer la légende si vous préférez
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            return context.parsed.y + "%";
                        }'
                    ]
                ]
            ]
        ];
    }

    // Méthode pour générer des couleurs uniques basées sur le nom du type
    private function generateColor(string $name): string
    {
        $colors = [
            'rgb(234, 179, 8)',    // jaune
            'rgb(34, 197, 94)',     // vert
            'rgb(59, 130, 246)',    // bleu
            'rgb(239, 68, 68)',     // rouge
            'rgb(79, 70, 229)',     // violet
            'rgb(249, 115, 22)',    // orange
            'rgb(6, 182, 212)',     // cyan
            'rgb(236, 72, 153)',    // rose
        ];

        $index = crc32($name) % count($colors);
        return $colors[$index];
    }

}
