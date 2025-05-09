<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Equipement;
use App\Jobs\UpdateEquipementStatus;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');



// mettre à jour l'état de la premier 'équipement par l'état très mauvaise

// Schedule::call(function () {
//     $equipement = \App\Models\Equipement::first();
//     $equipement->update(['etat' => 'hello world']);
// })->everyMinute(); // Changez la fréquence selon vos besoins


Schedule::job(new \App\Jobs\UpdateEquipementStatus())->daily();


