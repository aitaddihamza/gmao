<?php

namespace App\Filament\SharedPages\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\SharedWidgets\Widgets\MaintenanceStatsWidget;
use App\Filament\SharedWidgets\Widgets\EquipementStatsWidget;
use App\Filament\SharedWidgets\Widgets\TauxPanneWidget;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.shared.pages.dashboard';

    protected static ?string $navigationLabel = 'Stats';
    // cange the tilte
    protected static ?string $title = 'Stats';

    protected function getHeaderWidgets(): array
    {
        return [
            MaintenanceStatsWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            EquipementStatsWidget::class,
            TauxPanneWidget::class,
        ];
    }





}
