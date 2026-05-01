<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Collecte;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public function getStatsProperty()
    {
        $collectes = Collecte::where('created_by', Auth::id())
            ->orWhereHas('utilisateurs', fn($q) => $q->where('user_id', Auth::id()))
            ->get();

        return [
            'total' => $collectes->count(),
            'active' => $collectes->where('status', 'active')->count(),
            'brouillon' => $collectes->where('status', 'brouillon')->count(),
            'fermee' => $collectes->where('status', 'fermee')->count(),
        ];
    }

    public function getRecentCollectesProperty()
    {
        return Collecte::where('created_by', Auth::id())
            ->orWhereHas('utilisateurs', fn($q) => $q->where('user_id', Auth::id()))
            ->latest()
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard')->layout('layouts.app');
    }
}
