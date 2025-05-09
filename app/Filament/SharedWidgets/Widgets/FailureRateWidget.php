<?php

namespace App\Filament\SharedWidgets\Widgets;

use App\Models\Ticket;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class FailureRateWidget extends ChartWidget
{
    protected static ?string $heading = 'Taux de pannes';
    protected static ?string $pollingInterval = '30s';

    protected function getData(): array
    {
        $currentYear = Carbon::now()->year;

        $monthlyFailures = Ticket::whereYear('created_at', $currentYear)
            ->where('type_ticket', 'correctif')
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();


        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $data[] = $monthlyFailures[$i] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Nombre de pannes',
                    'data' => $data,
                    'backgroundColor' => 'rgb(239, 68, 68)', // Red
                ],
            ],
            'labels' => [
                'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    public static function canView(): bool
    {
        return true; // Adjust this based on your authorization logic
    }
}
