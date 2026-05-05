<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Collecte;
use App\Models\DonneeCollecte;

class AdminDashboard extends Component
{
    public function getStats()
    {
        return [
            'total_users' => User::count(),
            'total_collectes' => Collecte::count(),
            'total_donnees' => DonneeCollecte::count(),
            'active_collectes' => Collecte::where('status', 'active')->count(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'new_donnees_today' => DonneeCollecte::whereDate('created_at', today())->count(),
        ];
    }

    public function render()
    {
        return view('livewire.admin.admin-dashboard', [
            'stats' => $this->getStats(),
        ])->layout('layouts.app');
    }
}
