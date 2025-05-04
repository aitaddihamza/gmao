<?php

namespace App\Filament\Engineer\Widgets;

use App\Filament\SharedResources\MaintenancePreventive\MaintenancePreventiveResource;
use App\Models\MaintenancePreventive;
use Saade\FilamentFullCalendar\Data\EventData;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Actions;

class CalendarWidget extends FullCalendarWidget
{
    // protected static string $view = 'filament.engineer.widgets.calendar-widget';

    public Model | string | null $model = MaintenancePreventive::class;



    protected function headerActions(): array
    {
        return [
            // Actions\CreateAction::disabled()
                // ->label('Planifier')
                // ->disabled()
                // ->icon('heroicon-o-plus')
                // ->url(MaintenancePreventiveResource::getUrl('create'))
                // ->color('primary')
            // ,
        ];
    }

    protected function modalActions(): array
    {
        return [
            Actions\EditAction::make()
        ];
    }
    public function eventDidMount(): string
    {
        return <<<JS
    function({ event, el }) {
        // On récupère le statut via event.extendedProps
        const status = (event.extendedProps?.statut ?? '').toLowerCase();

        // Dictionnaire de correspondance statut -> couleur
        const colors = {
            'planifiee': '#3b82f6',   // info (bleu)
            'en_attente': '#facc15',  // warning (jaune)
            'en_cours': '#6366f1',    // primary (indigo)
            'terminee': '#10b981',    // success (vert)
            'reportee': '#9ca3af',    // gray (gris)
            'annulee': '#ef4444'      // danger (rouge)
        };

        const background = colors[status] ?? '#3b82f6'; // couleur par défaut (info)

        el.style.backgroundColor = background;
        el.style.border = 'none';
        el.style.color = 'white';

        el.setAttribute("x-tooltip", "tooltip");
        el.setAttribute("x-data", "{ tooltip: '"+event.title+"' }");
    }
    JS;
    }


    public function fetchEvents(array $fetchInfo): array
    {
        return MaintenancePreventive::query()
            ->where('date_planifiee', '>=', $fetchInfo['start'])
            ->where('date_planifiee', '<=', $fetchInfo['end'])
            ->get()
            ->map(
                fn (MaintenancePreventive $mp) => EventData::make()
                ->id($mp->id)
                ->title($mp->equipement->designation . " - " . $mp->statut)
                ->backgroundColor('transparent') // pour éviter d’écraser via PHP
                ->start($mp->date_planifiee)
                ->end($mp->date_planifiee)
                ->url(
                    url: MaintenancePreventiveResource::getUrl(name: 'view', parameters: ['record' => $mp]),
                    shouldOpenUrlInNewTab: true
                )
                ->extendedProps([
                    'statut' => $mp->statut,
                ])
            )
            ->toArray();
    }

    public static function canView(): bool
    {
        return true;
    }

    public function config(): array
    {
        return [
            'firstDay' => 1,
            'headerToolbar' => [
                'left' => 'dayGridWeek,dayGridMonth',
                'center' => 'title',
                'right' => 'prev,next today',
            ],
            // Ne pas afficher l'heure dans les événements
            'eventTimeFormat' => [
                'hour' => '2-digit',
                'minute' => '2-digit',
                'meridiem' => false,
                'hour12' => false,
            ],
            // Facultatif : supprimer complètement l'heure en ne la formatant pas du tout
            'displayEventTime' => false,
        ];
    }




}
