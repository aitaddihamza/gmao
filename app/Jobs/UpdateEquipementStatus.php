<?php 

namespace App\Jobs;

use App\Models\Equipement;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateEquipementStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    
    public function __construct()
    {
        $this->handle();
    }

    public function handle(): void
    {
        $today = Carbon::today();

        // Récupérer tous les équipements
        $equipements = \App\Models\Equipement::with('maintenancePreventives')->get();

        foreach ($equipements as $equipement) {
            // Vérifie s'il a au moins une maintenance active aujourd’hui
            $maintenance = $equipement->maintenancePreventives->contains(function ($maintenance) use ($today) {
                if($today->between(Carbon::parse($maintenance->date_debut), Carbon::parse($maintenance->date_fin)))
                {
                    return $maintenance;
                } else  {
                    return null;
                }

            });
            // Met à jour l'état
            $nouvelEtat = $maintenance ? 'hors_service' : $equipement->etat;

            if ($equipement->etat !== $nouvelEtat) {
                $equipement->update(['etat' => $nouvelEtat]);
                // notifier le créateur de la maintenance qu'il faut faire une maintenance préventive
            }
        }
    }



}
