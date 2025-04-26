<?php

namespace App\Providers;

use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\ServiceProvider;

class FilamentNotificationSoundServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        FilamentAsset::register([
            Js::make('notification-sound', resource_path('js/filament/notification-sound.js')),
        ]);
    }
}
