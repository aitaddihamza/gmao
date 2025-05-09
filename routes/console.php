<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\UpdateEquipementStatus;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');




Schedule::when(function () {
    try {
        return Schema::hasTable('equipements');
    } catch (\Throwable $e) {
        // Prevent crash during migration
        return false;
    }
}, function () {
    Schedule::job(new UpdateEquipementStatus())->daily();
});
