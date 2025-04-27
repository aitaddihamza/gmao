<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class UsersChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Répartition des utilisateurs par rôle (Année courante)';
    protected static ?string $pollingInterval = '30s';
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $roles = ['technicien', 'ingenieur', 'responsable', 'majeur', 'chef'];
        $currentYear = Carbon::now()->year;

        $roleLabels = [
            'technicien' => 'Techniciens',
            'ingenieur' => 'Ingénieurs',
            'responsable' => 'Responsables',
            'majeur' => 'Majeurs',
            'chef' => 'Directeurs'
        ];

        $usersPerRole = User::whereYear('created_at', $currentYear)
            ->whereIn('role', $roles)
            ->selectRaw('role, count(*) as count')
            ->groupBy('role')
            ->pluck('count', 'role')
            ->toArray();

        $datasets = [];
        $labels = [];
        foreach ($roles as $role) {
            $labels[] = $roleLabels[$role];
            $datasets[] = $usersPerRole[$role] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Nombre d\'utilisateurs',
                    'data' => $datasets,
                    'backgroundColor' => [
                        'rgb(234, 179, 8)',    // warning - Techniciens
                        'rgb(34, 197, 94)',    // success - Ingénieurs
                        'rgb(59, 130, 246)',   // info - Responsables
                        'rgb(239, 68, 68)',    // danger - Majeurs
                        'rgb(79, 70, 229)'     // primary - Directeurs
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
} 