<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\File;

Schedule::command('livewire:clean-tmp')->hourly();

// Nettoie les fichiers temporaires Livewire toutes les heures
Schedule::call(function () {
    $tmpPath = storage_path('app/private/livewire-tmp');

    if (File::exists($tmpPath)) {
        $files = File::files($tmpPath);
        $deleted = count($files);
        File::cleanDirectory($tmpPath);

     }
})->hourly()->name('clean-livewire-tmp');Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
