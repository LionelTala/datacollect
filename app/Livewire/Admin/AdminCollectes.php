<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Collecte;
use App\Models\User;

class AdminCollectes extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 20;
    public $statusFilter = '';

    public function getCollectes()
    {
        $query = Collecte::with('createur');

        if (!empty($this->search)) {
            $query->where('nom', 'like', "%{$this->search}%")
                ->orWhereHas('createur', fn($q) => $q->where('name', 'like', "%{$this->search}%"));
        }

        if (!empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        return $query->orderBy('created_at', 'desc')->paginate($this->perPage);
    }



    public function delete($id)
    {
        Collecte::where('id', $id)->delete();
        session()->flash('success', 'Collecte supprimée');
    }

    public function changeStatus($id, $status)
    {
        Collecte::where('id', $id)->update(['status' => $status]);
        session()->flash('success', 'Statut modifié');
    }

    public function render()
    {
        return view('livewire.admin.admin-collectes', [
            'collectes' => $this->getCollectes(),
        ])->layout('layouts.app');
    }
}
