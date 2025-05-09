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
        $correctiveCount = Ticket::where('type_ticket', 'correctif')->count();

        return [
            Stat::make('Maintenances préventives', $preventiveCount)
                ->description('Total des maintenances préventives')
                ->color('success')
                ->chart([5, 6, 7, 8, 9, 10]),

            Stat::make('Maintenances correctives', $correctiveCount)
                ->description('Total des maintenances correctives')
                ->color('danger')
                ->chart([3, 4, 5, 6, 7, 8]),
        ];
    }

    public static function canView(): bool
    {
        return true; // Adjust this based on your authorization logic
    }
}
