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
    public Model | string | null $model = MaintenancePreventive::class;

    protected function headerActions(): array
    {
        return [
            // Actions existantes...
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
        const status = (event.extendedProps?.statut ?? '').toLowerCase();

        const colors = {
            'planifiee': '#3b82f6',
            'en_attente': '#facc15',
            'en_cours': '#6366f1',
            'terminee': '#10b981',
            'reportee': '#9ca3af',
            'annulee': '#ef4444'
        };

        const background = colors[status] ?? '#3b82f6';

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
                ->backgroundColor('transparent')
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
            'eventTimeFormat' => [
                'hour' => '2-digit',
                'minute' => '2-digit',
                'meridiem' => false,
                'hour12' => false,
            ],
            'displayEventTime' => false,
        ];
    }


}
