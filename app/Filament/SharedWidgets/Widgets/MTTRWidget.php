<?php 


namespace App\Filament\SharedWidgets\Widgets;

use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MTTRWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    public function getStats(): array
    {
        $totalRepairTime = Ticket::where('type_ticket', 'correctif')->sum('temps_arret'); // Replace with actual field
        $repairCount = Ticket::where('type_ticket', 'correctif')->count();

        $mttr = $repairCount > 0 ? round($totalRepairTime / $repairCount, 2) : 0;

        return [
            Stat::make('MTTR', "{$mttr} heures")
                ->description('Temps moyen de réparation')
                ->color('warning')
                ->chart([5, 10, 15, 20, 25, 30]),
        ];
    }
}
