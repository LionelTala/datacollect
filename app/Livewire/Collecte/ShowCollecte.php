<?php

namespace App\Livewire\Collecte;

use Livewire\Component;
use App\Models\Collecte;
use App\Models\DonneeCollecte;
use Illuminate\Support\Facades\Auth;

class ShowCollecte extends Component
{
    public $collecte;
    public $activeTab = 'form';

    public function mount($id)
    {
        $this->collecte = Collecte::with('createur')->findOrFail($id);
        $this->authorizeAccess();
    }

    public function authorizeAccess()
    {
        $isCreator = $this->collecte->created_by == Auth::id();
        $isMember = $this->collecte->utilisateurs()->where('user_id', Auth::id())->exists();

        if (!$isCreator && !$isMember && !Auth::user()->is_admin) {
            abort(403, 'Accès non autorisé');
        }
    }

    public function getUserRole()
    {
        $pivot = $this->collecte->utilisateurs()->where('user_id', Auth::id())->first();
        return $pivot ? $pivot->pivot->role : null;
    }

    public function canSeeData()
    {
        $role = $this->getUserRole();
        return in_array($role, ['superviseur', 'analyste']) || $this->collecte->created_by == Auth::id() || Auth::user()->is_admin;
    }

    public function canExport()
    {
        $role = $this->getUserRole();
        return in_array($role, ['superviseur', 'analyste']) || $this->collecte->created_by == Auth::id() || Auth::user()->is_admin;
    }

    public function getStats()
    {
        $donnees = DonneeCollecte::where('collecte_id', $this->collecte->id)->count();
        $users = DonneeCollecte::where('collecte_id', $this->collecte->id)->distinct('user_id')->count();
        $lastWeek = DonneeCollecte::where('collecte_id', $this->collecte->id)
            ->where('created_at', '>=', now()->subWeek())->count();

        return [
            'total' => $donnees,
            'users' => $users,
            'last_week' => $lastWeek,
        ];
    }

    public function render()
    {
        return view('livewire.collecte.show-collecte', [
            'stats' => $this->getStats(),
        ])->layout('layouts.app');
    }
}
