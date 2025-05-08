<?php

namespace App\Filament\SharedWidgets\Widgets;

use App\Filament\SharedResources\MaintenancePreventive\MaintenancePreventiveResource;
use App\Models\MaintenancePreventive;
use Saade\FilamentFullCalendar\Data\EventData;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;

class CalendarWidget extends FullCalendarWidget
{
    public Model | string | null $model = MaintenancePreventive::class;


    protected function headerActions(): array
    {
        return [
            //
        ];
    }


    public function getFormSchema(): array
    {
        return [
            // date début and date fin with the updatd values
            Forms\Components\DatePicker::make('date_debut')
                ->label('Date de début')
                ->required()
                ->default(fn ($record, array $arguments) => $arguments['event']['start'] ?? $record->date_debut)
                ->displayFormat('Y-m-d H:i:s')
                ->placeholder('Sélectionner une date de début'),
            Forms\Components\DatePicker::make('date_fin')
                ->label('Date de fin')
                ->required()
                ->default(fn ($record, array $arguments) => $arguments['event']['end'] ?? $record->date_fin)
                ->displayFormat('Y-m-d H:i:s')
                ->placeholder('Sélectionner une date de fin'),
        ];
    }


    protected function modalActions(): array
    {
        return [
            // rempli automatiquement les dates par les valeurs de l'événement
            Actions\EditAction::make()
                ->mountUsing(
                    function (Forms\Form $form, array $arguments) {
                        $form->fill([
                            'date_debut' => $arguments['event']['start'],
                            'date_fin' => $arguments['event']['end'],
                        ]);
                    }
                ),
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
            'termine': '#10b981',
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
            el.style.pointerEvents = 'none';
            event.setProp('editable', false); // Disable editing programmatically
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
                    'isCreator' => $mp->user_createur_id == auth()->id(),
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
            'editable' => true,
            'selectable' => true,
            'selectMirror' => true,
            'select' => 'function(info) {
                $wire.dispatch("open-modal", { id: "create-maintenance", arguments: { start: info.startStr, end: info.endStr } });
            }',
        ];
    }

    protected function getCreateFormSchema(): array
    {
        return [
            Forms\Components\Select::make('equipement_id')
                ->relationship('equipement', 'designation')
                ->required()
                ->label('Équipement'),
            Forms\Components\DatePicker::make('date_debut')
                ->required()
                ->label('Date de début'),
            Forms\Components\DatePicker::make('date_fin')
                ->required()
                ->label('Date de fin'),
            Forms\Components\Textarea::make('description')
                ->required()
                ->label('Description'),
            Forms\Components\Hidden::make('statut')
                ->default('planifiee'),
            Forms\Components\Hidden::make('user_createur_id')
                ->default(fn () => auth()->id()),
        ];
    }

    public function create(): void
    {
        $data = $this->form->getState();
        
        $maintenance = MaintenancePreventive::create($data);

        Notification::make()
            ->title('Maintenance préventive créée avec succès')
            ->success()
            ->send();

        $this->dispatch('refresh-calendar');
    }
}
