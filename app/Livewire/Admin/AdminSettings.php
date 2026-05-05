<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class AdminSettings extends Component
{
    public $site_name = 'DataCollect';
    public $site_description = 'Plateforme de collecte de données collaborative';
    public $maintenance_mode = false;
    public $registration_open = true;

    public function mount()
    {
        $this->site_name = config('app.name');
        $this->maintenance_mode = app()->isDownForMaintenance();
    }

    public function toggleMaintenance()
    {
        if ($this->maintenance_mode) {
            Artisan::call('up');
            $this->maintenance_mode = false;
            session()->flash('success', 'Site réactivé');
        } else {
            Artisan::call('down --retry=60 --secret=datacollect');
            $this->maintenance_mode = true;
            session()->flash('success', 'Site en maintenance. Accès via /datacollect');
        }
    }

    public function clearCache()
    {
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Cache::flush();
        session()->flash('success', 'Cache vidé avec succès');
    }

    public function render()
    {
        return view('livewire.admin.admin-settings')->layout('layouts.app');
    }
}
