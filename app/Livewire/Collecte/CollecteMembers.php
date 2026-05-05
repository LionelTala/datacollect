<?php

namespace App\Livewire\Collecte;

use Livewire\Component;
use App\Models\Collecte;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CollecteMembers extends Component
{
    public $collecte;
    public $invitationEmail = '';
    public $invitationRole = 'collecteur';

    public function mount($collecteId)
    {
        $this->collecte = Collecte::findOrFail($collecteId);
    }

    public function sendInvitation()
    {
        $user = User::where('email', $this->invitationEmail)->first();

        if (!$user) {
            session()->flash('error', 'Utilisateur non trouvé.');
            return;
        }

        $exists = $this->collecte->utilisateurs()->where('user_id', $user->id)->exists();
        if ($exists) {
            session()->flash('error', 'Déjà membre');
            return;
        }

        $this->collecte->utilisateurs()->attach($user->id, ['role' => $this->invitationRole]);
        session()->flash('success', 'Membre ajouté');
        $this->invitationEmail = '';
    }

    public function removeMember($userId)
    {
        if ($this->collecte->created_by == Auth::id() || Auth::user()->is_admin) {
            $this->collecte->utilisateurs()->detach($userId);
            session()->flash('success', 'Membre retiré');
        }
    }

    public function changeRole($userId, $role)
    {
        if ($this->collecte->created_by == Auth::id()) {
            $this->collecte->utilisateurs()->updateExistingPivot($userId, ['role' => $role]);
            session()->flash('success', 'Rôle modifié');
        }
    }

    public function render()
    {
        $this->collecte->load('utilisateurs');
        return view('livewire.collecte.collecte-members', [
            'members' => $this->collecte->utilisateurs,
        ]);
    }
}
