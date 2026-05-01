<?php

namespace App\Livewire\Analyse;

use Livewire\Component;
use App\Models\Collecte;
use App\Models\DonneeCollecte;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnalyseCollecte extends Component
{
    public $collecte;
    public $activeChart = 'bar';
    public $selectedField = null;
    public $dateRange = 'all';
    public $startDate = null;
    public $endDate = null;

    public function mount($id)
    {
        $this->collecte = Collecte::with('createur')->findOrFail($id);
        $this->authorizeAccess();

        // Premier champ select/radio par défaut
        foreach ($this->collecte->config_schema as $field) {
            if (in_array($field['type'], ['select', 'radio'])) {
                $this->selectedField = $field['name'];
                break;
            }
        }
    }

    public function authorizeAccess()
    {
        $isCreator = $this->collecte->created_by == Auth::id();
        $isMember = $this->collecte->utilisateurs()->where('user_id', Auth::id())->exists();
        $role = $this->getUserRole();
        $isAnalyst = in_array($role, ['analyste', 'superviseur']);

        if (!$isCreator && !$isMember && !$isAnalyst && !Auth::user()->is_admin) {
            abort(403, 'Accès non autorisé - Espace analyste uniquement');
        }
    }

    public function getUserRole()
    {
        $pivot = $this->collecte->utilisateurs()->where('user_id', Auth::id())->first();
        return $pivot ? $pivot->pivot->role : null;
    }

    public function getDonnees()
    {
        $query = DonneeCollecte::where('collecte_id', $this->collecte->id);

        if ($this->dateRange == 'week') {
            $query->where('created_at', '>=', now()->subWeek());
        } elseif ($this->dateRange == 'month') {
            $query->where('created_at', '>=', now()->subMonth());
        } elseif ($this->dateRange == 'custom' && $this->startDate) {
            $query->where('created_at', '>=', $this->startDate);
            if ($this->endDate) $query->where('created_at', '<=', $this->endDate);
        }

        return $query->with('utilisateur')->get();
    }

    public function getChartData()
    {
        $donnees = $this->getDonnees();
        $field = $this->selectedField;

        if (!$field || !isset($this->collecte->config_schema[$field])) {
            return ['labels' => [], 'values' => [], 'title' => ''];
        }

        $fieldConfig = $this->collecte->config_schema[$field];
        $counts = [];

        foreach ($donnees as $donnee) {
            $value = $donnee->data[$field] ?? 'Non renseigné';
            if (is_array($value)) $value = json_encode($value);
            if ($value === '' || $value === null) $value = 'Non renseigné';

            if (!isset($counts[$value])) $counts[$value] = 0;
            $counts[$value]++;
        }

        arsort($counts);

        return [
            'labels' => array_keys($counts),
            'values' => array_values($counts),
            'title' => $fieldConfig['label']
        ];
    }

    public function getStats()
    {
        $donnees = $this->getDonnees();

        return [
            'total' => $donnees->count(),
            'users' => $donnees->pluck('user_id')->unique()->count(),
            'last_week' => $donnees->where('created_at', '>=', now()->subWeek())->count(),
            'last_month' => $donnees->where('created_at', '>=', now()->subMonth())->count(),
        ];
    }

    public function exportCSV()
    {
        $donnees = $this->getDonnees();
        $filename = 'analyse_' . $this->collecte->nom . '_' . date('Y-m-d') . '.csv';

        $handle = fopen('php://temp', 'w+');
        fputcsv($handle, ['ID', 'Utilisateur', 'Date', 'Données'], ';');

        foreach ($donnees as $donnee) {
            fputcsv($handle, [
                $donnee->id,
                $donnee->utilisateur->name,
                $donnee->created_at->format('d/m/Y H:i'),
                json_encode($donnee->data, JSON_UNESCAPED_UNICODE)
            ], ';');
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response()->streamDownload(function() use ($content) {
            echo $content;
        }, $filename, ['Content-Type' => 'text/csv; charset=utf-8']);
    }

    public function render()
    {
        return view('livewire.analyse.analyse-collecte', [
            'stats' => $this->getStats(),
            'chartData' => $this->getChartData(),
            'donnees' => $this->getDonnees(),
        ])->layout('layouts.app');
    }
}
