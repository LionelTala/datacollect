<?php

namespace App\Livewire\Collecte;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Collecte;
use App\Models\DonneeCollecte;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CollecteData extends Component
{
    use WithPagination;

    public $collecte;
    public $perPage = 20;
    public $search = '';

    public function mount($collecteId)
    {
        $this->collecte = Collecte::findOrFail($collecteId);
    }

    public function deleteDonnee($donneeId)
    {
        $donnee = DonneeCollecte::findOrFail($donneeId);
        $canDelete = ($this->collecte->created_by == Auth::id()) || ($donnee->user_id == Auth::id());

        if ($canDelete) {
            // Supprimer les fichiers associés
            foreach ($donnee->fichiers_processes ?? [] as $file) {
                if (isset($file['original']) && Storage::disk('public')->exists($file['original'])) {
                    Storage::disk('public')->delete($file['original']);
                }
            }
            $donnee->delete();
            session()->flash('success', 'Donnée supprimée');
        }
    }

    public function getDonneesQuery()
    {
        $query = DonneeCollecte::where('collecte_id', $this->collecte->id)->with('utilisateur');

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('id', 'like', "%{$this->search}%")
                  ->orWhereHas('utilisateur', fn($u) => $u->where('name', 'like', "%{$this->search}%"));
            });
        }

        return $query->latest();
    }

    public function getTopContributors()
    {
        return DonneeCollecte::where('collecte_id', $this->collecte->id)
            ->select('user_id', \DB::raw('count(*) as total'))
            ->with('utilisateur')
            ->groupBy('user_id')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.collecte.collecte-data', [
            'donnees' => $this->getDonneesQuery()->paginate($this->perPage),
            'topContributors' => $this->getTopContributors(),
        ]);
    }
}
