<?php

namespace App\Livewire\Collecte;

use Livewire\Component;
use App\Models\Collecte;
use App\Models\DonneeCollecte;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ListCollectes extends Component
{
    public $filter = 'all';

    // Pour la modal des participants
    public $showParticipantsModal = false;
    public $currentCollecteId = null;
    public $currentCollecteNom = '';
    public $invitationEmail = '';
    public $invitationRole = 'collecteur';
    public $members = [];

    public function getCollectesProperty()
    {
        $query = Collecte::query();

        if ($this->filter == 'created') {
            $query->where('created_by', Auth::id());
        } elseif ($this->filter == 'invited') {
            $query->whereHas('utilisateurs', function($q) {
                $q->where('user_id', Auth::id());
            })->where('created_by', '!=', Auth::id());
        } else {
            $query->where(function($q) {
                $q->where('created_by', Auth::id())
                  ->orWhereHas('utilisateurs', function($sub) {
                      $sub->where('user_id', Auth::id());
                  });
            });
        }

        return $query->with('createur')->latest()->get();
    }

    public function getTotalAllProperty()
    {
        return Collecte::where('created_by', Auth::id())
            ->orWhereHas('utilisateurs', fn($q) => $q->where('user_id', Auth::id()))
            ->count();
    }

    public function getTotalCreatedProperty()
    {
        return Collecte::where('created_by', Auth::id())->count();
    }

    public function getTotalInvitedProperty()
    {
        return Collecte::whereHas('utilisateurs', fn($q) => $q->where('user_id', Auth::id()))
            ->where('created_by', '!=', Auth::id())
            ->count();
    }

    /**
     * Récupérer le nombre de données collectées pour une collecte
     */
    public function getDonneesCount($collecteId)
    {
        return DonneeCollecte::where('collecte_id', $collecteId)->count();
    }

    /**
     * Récupérer le nombre de participants uniques pour une collecte
     */
    public function getParticipantsCount($collecteId)
    {
        return DonneeCollecte::where('collecte_id', $collecteId)
            ->distinct('user_id')
            ->count('user_id');
    }

    /**
     * Ouvrir la modal de gestion des participants
     */
    public function openManageParticipants($collecteId)
    {
        $this->currentCollecteId = $collecteId;
        $collecte = Collecte::findOrFail($collecteId);
        $this->currentCollecteNom = $collecte->nom;
        $this->loadMembers();
        $this->showParticipantsModal = true;
        $this->dispatch('open-modal', 'participantsModal');
    }

    /**
     * Charger les membres de la collecte
     */
    public function loadMembers()
    {
        $collecte = Collecte::with('utilisateurs')->find($this->currentCollecteId);
        $this->members = $collecte->utilisateurs;
    }

    /**
     * Ajouter un participant
     */
    public function addParticipant()
    {
        $this->validate([
            'invitationEmail' => 'required|email',
            'invitationRole' => 'in:collecteur,superviseur,analyste',
        ]);

        $user = User::where('email', $this->invitationEmail)->first();

        if (!$user) {
            session()->flash('error', 'Utilisateur non trouvé.');
            return;
        }

        $collecte = Collecte::find($this->currentCollecteId);

        $exists = $collecte->utilisateurs()->where('user_id', $user->id)->exists();
        if ($exists) {
            session()->flash('error', 'Cet utilisateur fait déjà partie de la collecte');
            return;
        }

        $collecte->utilisateurs()->attach($user->id, ['role' => $this->invitationRole]);

        session()->flash('success', 'Utilisateur ajouté avec succès !');
        $this->invitationEmail = '';
        $this->loadMembers();
    }

    /**
     * Retirer un participant
     */
    public function removeParticipant($userId)
    {
        $collecte = Collecte::find($this->currentCollecteId);
        $collecte->utilisateurs()->detach($userId);
        session()->flash('success', 'Participant retiré');
        $this->loadMembers();
    }

    /**
     * Changer le rôle d'un participant
     */
    public function changeRole($userId, $role)
    {
        $collecte = Collecte::find($this->currentCollecteId);
        $collecte->utilisateurs()->updateExistingPivot($userId, ['role' => $role]);
        session()->flash('success', 'Rôle modifié');
        $this->loadMembers();
    }

    /**
     * Exporter la collecte
     */
    public function exportCollecte($collecteId)
    {
        $collecte = Collecte::findOrFail($collecteId);
        $donnees = DonneeCollecte::where('collecte_id', $collecteId)->with('utilisateur')->get();

        $filename = 'collecte_' . str_replace(' ', '_', $collecte->nom) . '_' . date('Y-m-d') . '.csv';

        $handle = fopen('php://temp', 'w+');

        // En-têtes
        $headers = ['ID', 'Utilisateur', 'Email', 'Date'];
        foreach ($collecte->config_schema as $field) {
            $headers[] = $field['label'] . ' (' . $field['name'] . ')';
        }
        fputcsv($handle, $headers, ';');

        // Données
        foreach ($donnees as $donnee) {
            $row = [
                $donnee->id,
                $donnee->utilisateur->name,
                $donnee->utilisateur->email,
                $donnee->created_at->format('d/m/Y H:i')
            ];
            foreach ($collecte->config_schema as $field) {
                $value = $donnee->data[$field['name']] ?? '';
                if (is_array($value)) {
                    $value = json_encode($value);
                }
                $row[] = $value;
            }
            fputcsv($handle, $row, ';');
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response()->streamDownload(function() use ($content) {
            echo $content;
        }, $filename, ['Content-Type' => 'text/csv; charset=utf-8']);
    }

    /**
     * Archiver une collecte
     */
    public function archiveCollecte($collecteId)
    {
        $collecte = Collecte::findOrFail($collecteId);
        $collecte->status = 'fermee';
        $collecte->save();
        session()->flash('success', 'Collecte archivée');
    }
    public function reactiverCollecte($collecteId)
    {
        $collecte = Collecte::findOrFail($collecteId);
        $collecte->status = 'active';
        $collecte->save();
        session()->flash('success', 'Collecte réactivée');
    }

    /**
     * Mettre en brouillon
     */
    public function mettreEnBrouillon($collecteId)
    {
        $collecte = Collecte::findOrFail($collecteId);
        $collecte->status = 'brouillon';
        $collecte->save();
        session()->flash('success', 'Collecte mise en brouillon');
    }

    public function render()
    {
        return view('livewire.collecte.list-collectes', [
            'collectes' => $this->collectes,
            'totalAll' => $this->totalAll,
            'totalCreated' => $this->totalCreated,
            'totalInvited' => $this->totalInvited,
        ])->layout('layouts.app');
    }
}
