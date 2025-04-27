<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        return [
            Stat::make('Techniciens', User::where('role', 'technicien')->count())
                ->description('Utilisateurs avec rôle technicien')
                ->descriptionIcon('heroicon-m-wrench')
                ->color('warning')
                ->chart([7, 3, 4, 5, 6, 3, 5]),

            Stat::make('Ingénieurs', User::where('role', 'ingenieur')->count())
                ->description('Utilisateurs avec rôle ingénieur')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success')
                ->chart([2, 3, 4, 3, 4, 3, 4]),

            Stat::make('Responsables', User::where('role', 'responsable')->count())
                ->description('Utilisateurs avec rôle responsable')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info')
                ->chart([1, 2, 1, 2, 1, 2, 1]),

            Stat::make('Majeurs', User::where('role', 'majeur')->count())
                ->description('Utilisateurs avec rôle majeur')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('danger')
                ->chart([1, 1, 1, 1, 2, 1, 1]),

            Stat::make('Directeurs', User::where('role', 'chef')->count())
                ->description('Utilisateurs avec rôle directeur')
                ->descriptionIcon('heroicon-m-building-library')
                ->color('primary')
                ->chart([1, 1, 1, 1, 1, 1, 1]),
        ];
    }
} 