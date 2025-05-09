<?php

namespace App\Filament\SharedWidgets\Widgets;

use App\Filament\SharedResources\MaintenancePreventive\MaintenancePreventiveResource;
use App\Models\MaintenancePreventive;
use Saade\FilamentFullCalendar\Data\EventData;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Actions;
use Filament\Forms;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Illuminate\Support\Facades\Log;

class CalendarWidget extends FullCalendarWidget
{
    public Model | string | null $model = MaintenancePreventive::class;
    
    // Variable pour forcer le rafraichissement
    public $refreshCalendar = false;

    // Méthode pour forcer le rafraîchissement du calendrier
    public function refreshCalendar()
    {
        $this->refreshCalendar = !$this->refreshCalendar;
        $this->js('window.dispatchEvent(new Event("filament-full-calendar::refresh"))');
    }

    protected function headerActions(): array
    {
        if (auth()->user()->role != "engineer") {
            return [];
        }
        return [
            Actions\CreateAction::make()
                ->label('Planifier')
                ->icon('heroicon-o-plus')
                ->mountUsing(
                    function (Forms\Form $form, array $arguments) {
                        $form->fill([
                            'date_debut' => @$arguments['start'] ?? null,
                            'date_fin' => @$arguments['end'] ?? null,
                            'description' => null,
                            'equipement_id' => null,
                            'user_createur_id' => auth()->id(),
                            'tyepe_externe' => false,
                            'statut' => 'planifiee',
                            'fournisseur' => null,
                        ]);
                    }
                )
                ->action(function (array $data, Forms\Form $form): void {
                    // Créer l'enregistrement
                    $record = new MaintenancePreventive($data);
                    $record->save();
                    
                    // Envoyer la notification
                    try {
                        Log::info('Tentative d\'envoi de notification pour la maintenance: ' . $record->id);

                        $technicienResponsable = $record->assignee;
                        Log::info('Technicien responsable: ' . ($technicienResponsable ? $technicienResponsable->id : 'non assigné'));

                        if ($technicienResponsable) {
                            $userRole = $technicienResponsable->role;
                            Log::info('Rôle du technicien: ' . $userRole);

                            $notification = Notification::make()
                                ->title('Maintenance Preventive Planifiée')
                                ->body("Vous êtes affecté à une nouvelle maintenance préventive planifiée")
                                ->success()
                                ->actions([
                                    Action::make('Voir plus')
                                        ->url(route('filament.'.$userRole.'.resources.maintenance-preventive.view', $record->id))
                                        ->icon('heroicon-o-eye'),
                                ]);

                            $notification->sendToDatabase($technicienResponsable);
                            Log::info('Notification envoyée avec succès');
                        } else {
                            Log::warning('Aucun technicien assigné pour la maintenance: ' . $record->id);
                        }
                    } catch (\Exception $e) {
                        Log::error('Erreur lors de l\'envoi de la notification: ' . $e->getMessage());
                    }
                    
                    // Rafraîchir le calendrier directement
                    $this->refreshCalendar();
                    
                    // Notification de succès
                    Notification::make()
                        ->title('Maintenance créée avec succès')
                        ->success()
                        ->send();
                })
        ];
    }


    public function getFormSchema(): array
    {
        return [
            // date début and date fin with the updatd values
            Forms\Components\DatePicker::make('date_debut')
                ->label('Date de début')
                ->required()
              
                ->default(fn ($record, array $arguments) => Carbon::parse($arguments['start'])->format('Y-m-d H:i:s') ?? $record->date_debut)
                ->displayFormat('Y-m-d H:i:s')
                ->placeholder('Sélectionner une date de début'),
            Forms\Components\DatePicker::make('date_fin')
                ->label('Date de fin')
                
                ->required()
                ->default(fn ($record, array $arguments) => $arguments['end'] ?? $record->date_fin)
                ->displayFormat('Y-m-d H:i:s')
                ->placeholder('Sélectionner une date de fin'),
            Forms\Components\Select::make('equipement_id')
                ->relationship('equipement', 'designation')
                ->required()
                ->searchable()
                ->preload()
                ->label('Équipement concerné')
                ->getOptionLabelFromRecordUsing(fn ($record) => $record->designation . ' - ' . $record->modele . ' - ' . $record->marque . ' - ' . $record->bloc->localisation),
            Forms\Components\Hidden::make('user_createur_id')
                ->default(fn () => auth()->id())
                ->required(),
                Forms\Components\Select::make('user_assignee_id')
                ->relationship('assignee', 'name')
                ->searchable()
                ->preload()
                ->label('Assigné à')
                ->getOptionLabelFromRecordUsing(fn ($record) => $record->name . ' ' . $record->prenom . ' - ' . $record->role),
            // toggle for type_externe
            Forms\Components\Toggle::make('type_externe')
                ->label('Type externe')
                ->reactive()
                ->default(fn ($record) => $record->type_externe ?? false),
            Forms\Components\TextInput::make('fournisseur')
                ->maxLength(255)
                ->required()
                ->placeholder('Nom du fournisseur')
                ->hidden(fn (Forms\Get $get) => !$get('type_externe')),
            Forms\Components\Textarea::make('description')
                ->label('Description')
                ->required()
                ->default(fn ($record) => $record->description ?? '')
                ->placeholder('Ajouter une description'),
            // hidden field for statut by default it's planfiee
            Forms\Components\Hidden::make('statut')
                ->default('planifiee'),
        ];
    }


    protected function modalActions(): array
    {
        return [
            // rempli automatiquement les dates par les valeurs de l'événement
            Actions\EditAction::make()
                ->mountUsing(
                    function (Forms\Form $form, array $arguments, MaintenancePreventive $record) {
                        $form->fill([
                            'date_debut' =>  Carbon::parse($arguments['event']['start'])->format('Y-m-d H:i:s'),
                            'date_fin' => Carbon::parse($arguments['event']['end'])->format('Y-m-d H:i:s'),
                            'description' => $record->description ?? null,
                            'equipement_id' => $arguments['event']['extendedProps']['equipement_id'] ?? null,
                            'user_createur_id' => auth()->id(),
                            'type_externe' => $record->type_externe ?? false,
                            'statut' => $record->statut ?? 'planifiee',
                            'fournisseur' => $record->fournisseur ?? null,
                            'user_assignee_id' => $record->user_assignee_id ?? null,
                        ]);
                    }
                )
                ->action(function (array $data, MaintenancePreventive $record): void {
                    // Mettre à jour l'enregistrement
                    $record->update($data);
                    
                    // Envoyer la notification
                    try {
                        Log::info('Tentative d\'envoi de notification pour la maintenance: ' . $record->id);

                        $technicienResponsable = $record->assignee;
                        Log::info('Technicien responsable: ' . ($technicienResponsable ? $technicienResponsable->id : 'non assigné'));

                        if ($technicienResponsable) {
                            $userRole = $technicienResponsable->role;
                            Log::info('Rôle du technicien: ' . $userRole);

                            $notification = Notification::make()
                                ->title('Maintenance Preventive Modifiée')
                                ->body("Une maintenance préventive a été modifiée")
                                ->success()
                                ->actions([
                                    Action::make('Voir plus')
                                        ->url(route('filament.'.$userRole.'.resources.maintenance-preventive.view', $record->id))
                                        ->icon('heroicon-o-eye'),
                                ]);

                            $notification->sendToDatabase($technicienResponsable);
                            Log::info('Notification envoyée avec succès');
                        } else {
                            Log::warning('Aucun technicien assigné pour la maintenance: ' . $record->id);
                        }
                    } catch (\Exception $e) {
                        Log::error('Erreur lors de l\'envoi de la notification: ' . $e->getMessage());
                    }
                    
                    // Rafraîchir le calendrier directement
                    $this->refreshCalendar();
                    
                    // Notification de succès
                    Notification::make()
                        ->title('Maintenance mise à jour avec succès')
                        ->success()
                        ->send();
                })
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
                ->end(Carbon::parse($mp->date_fin)->addHour())
                ->url(
                    url: MaintenancePreventiveResource::getUrl(name: 'view', parameters: ['record' => $mp])
                )
                ->extendedProps([
                    'statut' => $mp->statut,
                    'isCreator' => $mp->user_createur_id == auth()->id(),
                    'equipement_id' => $mp->equipement_id,
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
            'eventDidMount' => $this->eventDidMount(),
        ];
    }
    
    protected function getListeners(): array
    {
        return array_merge(parent::getListeners(), [
            'echo:*.MaintenancePreventiveCreated,MaintenancePreventiveCreated' => 'refreshCalendar',
            'echo:*.MaintenancePreventiveUpdated,MaintenancePreventiveUpdated' => 'refreshCalendar',
        ]);
    }
}