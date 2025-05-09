<?php

namespace App\Filament\SharedPages\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\SharedWidgets\Widgets\FailureRateWidget;
use App\Filament\SharedWidgets\Widgets\MaintenanceStatsWidget;
use App\Filament\SharedWidgets\Widgets\MTBFWidget;
use App\Filament\SharedWidgets\Widgets\MTTRWidget;


class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.shared.pages.dashboard';

    protected static ?string $navigationLabel = 'Tableau de bord';

    protected function getHeaderWidgets(): array
    {
        return [
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            MaintenanceStatsWidget::class,
            MTTRWidget::class,
            MTBFWidget::class,

        ];
    }



   
}
