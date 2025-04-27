<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\UserStatsWidget;
use App\Filament\Widgets\UsersChartWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.pages.dashboard';

    // change the navigation label to "Tableau de bord"
    protected static ?string $navigationLabel = 'Tableau de bord';
    protected static ?int $navigationSort = -1;

    protected function getHeaderWidgets(): array
    {
        return [
            UserStatsWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            UsersChartWidget::class,
        ];
    }
}
