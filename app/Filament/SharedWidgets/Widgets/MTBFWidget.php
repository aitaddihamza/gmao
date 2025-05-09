<?php
namespace App\Filament\SharedWidgets\Widgets;

use App\Models\Equipement;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MTBFWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $totalUptime = 10; // Replace with actual field
        $failureCount = 10; // Replace with actual field

        $mtbf = $failureCount > 0 ? round($totalUptime / $failureCount, 2) : 0;

        return [
            Stat::make('MTBF', "{$mtbf} heures")
                ->description('Temps moyen entre pannes')
                ->color('info')
                ->chart([10, 20, 30, 40, 50, 60]),
        ];
    }
}
