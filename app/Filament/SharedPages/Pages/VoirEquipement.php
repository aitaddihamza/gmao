<?php

namespace App\Filament\SharedPages\Pages;

use App\Filament\SharedWidgets\Widgets\EquipementStatsWidget;
use App\Filament\SharedWidgets\Widgets\EquipementChartWidget;
use Filament\Pages\Page;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Equipement;
use Carbon\Carbon;

class VoirEquipement extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    // change the slug to voir-equipement/{equipement}
    protected static ?string $slug = 'equipement/{id}';
    protected static bool $shouldRegisterNavigation = false;
    protected static string $view = 'filament.shared.pages.voir-equipement';


    public function getTitle(): string
    {
        return $this->equipement->designation . " - " . $this->equipement->modele . " - " . $this->equipement->marque;
    }

    protected function getTableQuery(): Builder
    {
        return Equipement::query();
    }


    public function mount(string $id): void
    {
        $this->equipement = Equipement::findOrFail($id);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            EquipementStatsWidget::make([
                'equipement' => $this->equipement
            ]),
        ];
    }


    public function getFooterWidgets(): array
    {
        return [
            EquipementChartWidget::make([
                'equipement' => $this->equipement
            ]),
        ];
    }



}
