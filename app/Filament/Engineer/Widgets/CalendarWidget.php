<?php

namespace App\Filament\Engineer\Widgets;

use App\Filament\SharedResources\MaintenancePreventive\MaintenancePreventiveResource;
use App\Models\MaintenancePreventive;
use Saade\FilamentFullCalendar\Data\EventData;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Actions;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;

class CalendarWidget extends FullCalendarWidget
{
    public Model | string | null $model = MaintenancePreventive::class;

    protected function headerActions(): array
    {
        return [
            //
        ];
    }

    protected function modalActions(): array
    {
        return [
            Actions\EditAction::make()
                ->form([
                    DateTimePicker::make('date_debut')
                        ->required()
                        ->label('Date de début'),
                    DateTimePicker::make('date_fin')
                        ->required()
                        ->label('Date de fin')
                ])
                ->visible(fn (MaintenancePreventive $record) => $record->user_createur_id === auth()->id())
        ];
    }

    public function eventDidMount(): string
    {
        return <<<JS
    function({ event, el }) {
        const status = (event.extendedProps?.statut ?? '').toLowerCase();
        const isCreator = event.extendedProps?.isCreator ?? false;

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
        
        if (!isCreator) {
            el.style.cursor = 'not-allowed';
            el.classList.add('fc-event-non-editable');
        }

        el.setAttribute("x-tooltip", "tooltip");
        el.setAttribute("x-data", "{ tooltip: '"+event.title+"' }");
    }
    JS;
    }

    public function fetchEvents(array $fetchInfo): array
    {
        return MaintenancePreventive::query()
            ->where(function ($query) use ($fetchInfo) {
                $query->whereBetween('date_debut', [$fetchInfo['start'], $fetchInfo['end']])
                    ->orWhereBetween('date_fin', [$fetchInfo['start'], $fetchInfo['end']])
                    ->orWhere(function ($q) use ($fetchInfo) {
                        $q->where('date_debut', '<=', $fetchInfo['start'])
                            ->where('date_fin', '>=', $fetchInfo['end']);
                    });
            })
            ->get()
            ->map(
                fn (MaintenancePreventive $mp) => EventData::make()
                ->id($mp->id)
                ->title($mp->equipement->designation . " - " . $mp->statut)
                ->backgroundColor('transparent')
                ->start($mp->date_debut)
                ->end($mp->date_fin)
                ->url(
                    url: MaintenancePreventiveResource::getUrl(name: 'view', parameters: ['record' => $mp])
                )
                ->extendedProps([
                    'statut' => $mp->statut,
                    'description' => $mp->description,
                    'assignee' => $mp->assignee?->name . ' ' . $mp->assignee?->prenom,
                    'isCreator' => $mp->user_createur_id === auth()->id(),
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
            'displayEventTime' => true,
            'selectable' => false,
            'selectMirror' => false,
            'dayMaxEvents' => true,
            'editable' => true,
            'droppable' => true,
            'eventOverlap' => false,
            'slotMinTime' => '08:00:00',
            'slotMaxTime' => '18:00:00',
            'allDaySlot' => false,
            'eventStartEditable' => true,
            'eventDurationEditable' => true,
            'eventConstraint' => 'businessHours',
            'eventAllow' => <<<JS
                function(dropInfo, draggedEvent) {
                    return draggedEvent.extendedProps.isCreator;
                }
            JS,
            'eventDrop' => <<<JS
                function(info) {
                    const event = info.event;
                    const isCreator = event.extendedProps.isCreator;
                    
                    if (!isCreator) {
                        info.revert();
                        return;
                    }

                    // Mettre à jour les dates dans le formulaire
                    const form = document.querySelector('form');
                    if (form) {
                        const dateDebutInput = form.querySelector('[name="date_debut"]');
                        const dateFinInput = form.querySelector('[name="date_fin"]');
                        
                        if (dateDebutInput && dateFinInput) {
                            const startDate = event.start.toISOString();
                            const endDate = event.end ? event.end.toISOString() : startDate;
                            
                            // Mettre à jour les valeurs des champs
                            dateDebutInput.value = startDate;
                            dateFinInput.value = endDate;
                            
                            // Déclencher les événements de changement
                            const changeEvent = new Event('change', { bubbles: true });
                            dateDebutInput.dispatchEvent(changeEvent);
                            dateFinInput.dispatchEvent(changeEvent);
                            
                            // Forcer la mise à jour du formulaire
                            if (typeof Livewire !== 'undefined') {
                                Livewire.find(form.closest('[wire\\:id]').getAttribute('wire:id'))
                                    .set('data.date_debut', startDate)
                                    .set('data.date_fin', endDate);
                            }
                        }
                    }
                }
            JS,
        ];
    }
}
