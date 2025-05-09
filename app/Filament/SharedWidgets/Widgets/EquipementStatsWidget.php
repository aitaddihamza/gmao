<?php

namespace App\Filament\SharedWidgets\Widgets;

use App\Models\Equipement;
use App\Models\Piece;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EquipementStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $total_equipements = Equipement::count();
        $hors_services = Equipement::where('etat', 'hors_service')->count();
        // total des équipements bon et acceptable
        $bons = Equipement::whereIn('etat', ['bon', 'acceptable'])->count();
        // total des pieces
        $pieces = Piece::count();


        return [
            Stat::make('Total des équipements', $total_equipements)
                ->description('Équipements enregistrés')
                ->descriptionIcon('heroicon-o-cube', 'before')
                ->color('success') // vert
                ->url(route('filament.' . auth()->user()->role . '.resources.equipements.index'))
                ->chart([5, 6, 7, 8, 9, 10]),

            Stat::make('Équipements hors service', $hors_services)
                ->description('Équipements hors service')
                ->descriptionIcon('heroicon-o-exclamation-triangle', 'before')
                ->color('danger') // rouge
                ->url(route('filament.' . auth()->user()->role . '.resources.equipements.index'))
                ->chart([3, 4, 5, 6, 7, 8]),

            Stat::make('Équipements fonctionnels', $bons)
                ->description('Équipements en bon état')
                ->descriptionIcon('heroicon-o-check-circle', 'before')
                ->color('warning') // jaune/orange pour "acceptable"
                ->url(route('filament.' . auth()->user()->role . '.resources.equipements.index'))
                ->chart([1, 2, 3, 4, 5, 6]),
            // pieces
            Stat::make('Équipements en pièces', $pieces)
                ->description('Pièces enregistrées')
                ->descriptionIcon('heroicon-o-wrench-screwdriver', 'before')
                ->color('secondary') // gris
                ->url(route('filament.' . auth()->user()->role . '.resources.pieces.index'))
                ->chart([1, 2, 3, 4, 5, 6]),
        ];
    }

    public static function canView(): bool
    {
        return true; // Adjust this based on your authorization logic
    }
}
