<?php

namespace App\Filament\SharedWidgets\Widgets;

use App\Models\MaintenancePreventive;
use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MaintenanceStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $preventiveCount = MaintenancePreventive::count();
        $pannesCount = Ticket::where('type_ticket', 'correctif')->count();
        $installationsCount = Ticket::where('type_ticket', 'installation')->count();
        // total des réparations (ticket correctif et statut est cloturé)
        $repairCount = Ticket::where('type_ticket', 'correctif')
            ->where('statut', 'cloturé')
            ->count();

        return [
            Stat::make('Total Maintenances préventives', $preventiveCount)
                ->description('maintenances préventives')
                ->url(route('filament.' . auth()->user()->role . '.resources.maintenance-preventive.index'))
                // don't use cog icon or wrench icon use something different
                // ->icon('heroicon-o-calendar')
                ->descriptionIcon('heroicon-o-calendar', 'before')
                ->color('success')
                ->chart([5, 6, 7, 8, 9, 10]),

            Stat::make('Total des pannes ', $pannesCount)
                ->description('pannes')
                ->url(route('filament.' . auth()->user()->role . '.resources.maintenance-correctives.index'))
                ->descriptionIcon('heroicon-o-exclamation-triangle', 'before')
                ->color('danger')
                ->chart([3, 4, 5, 6, 7, 8]),
            Stat::make('Total Installations', $installationsCount)
                ->description('Installations')
                ->descriptionIcon('heroicon-o-square-3-stack-3d', 'before')       // Pour l'empilement/assemblage
                ->url(route('filament.' . auth()->user()->role . '.resources.tickets.index'))
                ->color('warning')
                ->chart([1, 2, 3, 4, 5, 6]),
            // réparations
            Stat::make('Total Réparations', $repairCount)
                ->description('réparations')
                ->descriptionIcon('heroicon-o-wrench', 'before')
                ->url(route('filament.' . auth()->user()->role . '.resources.tickets.index'))
                ->color('danger')
                ->chart([1, 2, 3, 4, 5, 6]),
        ];
    }

    public static function canView(): bool
    {
        return true; // Adjust this based on your authorization logic
    }
}
