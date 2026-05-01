<?php

namespace App\Livewire\Collecte;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Collecte;
use App\Models\DonneeCollecte;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ShowCollecte extends Component
{
    use WithFileUploads;

    public $collecte;
    public $activeTab = 'form';
    public $formData = [];
    public $files = [];

    public $labels = [];
    public $newLabel = [
        'name' => '',
        'label' => '',
        'type' => 'text',
        'required' => false,
        'placeholder' => '',
        'options' => [],
        'option_labels' => [],
    ];
    public $tempOptionLabel = '';
    public $tempOptionValue = '';

    // Édition de champ
    public $editingFieldIndex = null;
    public $editField = [
        'name' => '',
        'label' => '',
        'type' => 'text',
        'required' => false,
        'placeholder' => '',
        'options' => [],
        'option_labels' => [],
    ];

    public $invitationEmail = '';
    public $invitationRole = 'collecteur';

    public function mount($id)
    {
        $this->collecte = Collecte::with('createur')->findOrFail($id);
        $this->labels = $this->collecte->config_schema;
    }

    public function canExport()
    {
        return $this->collecte->created_by == Auth::id() || Auth::user()->is_admin;
    }

    public function canSeeData()
    {
        return $this->collecte->created_by == Auth::id() || Auth::user()->is_admin;
    }

    public function getUserRole()
    {
        $pivot = $this->collecte->utilisateurs()->where('user_id', Auth::id())->first();
        return $pivot ? $pivot->pivot->role : null;
    }

    /**
     * Trouver le champ qui sert de catégorie pour le classement des fichiers
     */
    private function getCategoryField()
    {
        $categoryIndex = $this->collecte->category_field_index;

        if ($categoryIndex !== null && isset($this->collecte->config_schema[$categoryIndex])) {
            return $this->collecte->config_schema[$categoryIndex];
        }

        return null;
    }

    /**
     * Obtenir la valeur de la catégorie depuis les données du formulaire
     */
    private function getCategoryValue()
    {
        $categoryField = $this->getCategoryField();

        if ($categoryField) {
            $fieldName = $categoryField['name'];
            $value = $this->formData[$fieldName] ?? null;

            if (is_array($value)) {
                $value = reset($value);
            }

            if ($value === null || $value === '') {
                return 'non_classe';
            }

            $slug = Str::slug((string)$value, '_');
            return !empty($slug) ? $slug : 'non_classe';
        }

        return 'non_classe';
    }

    /**
     * Prétraitement des images
     */
    private function preprocessImage($file, $fieldConfig)
    {
        $categoryFolder = $this->getCategoryValue();
        $collecteSlug = Str::slug($this->collecte->nom, '_');

        $rules = $this->collecte->preprocess_rules['image'] ?? [
            'max_width' => 800,
            'max_height' => 600,
            'quality' => 85
        ];

        $filename = date('Ymd_His') . '_' . Str::random(8) . '.jpg';
        $path = 'collectes/' . $collecteSlug . '/images/' . $categoryFolder . '/' . $filename;

        $imageInfo = getimagesize($file->getRealPath());

        switch ($imageInfo[2]) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($file->getRealPath());
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($file->getRealPath());
                break;
            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($file->getRealPath());
                break;
            case IMAGETYPE_WEBP:
                $source = imagecreatefromwebp($file->getRealPath());
                break;
            default:
                $source = imagecreatefromjpeg($file->getRealPath());
        }

        $width = imagesx($source);
        $height = imagesy($source);

        $ratio = min($rules['max_width'] / $width, $rules['max_height'] / $height);
        $newWidth = round($width * $ratio);
        $newHeight = round($height * $ratio);

        $thumb = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        $fullPath = storage_path('app/public/' . $path);
        $directory = dirname($fullPath);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        imagejpeg($thumb, $fullPath, $rules['quality']);

        imagedestroy($source);
        imagedestroy($thumb);

        return [
            'original' => $path,
            'category' => $categoryFolder,
            'filename' => $filename,
            'dimensions' => ['width' => $newWidth, 'height' => $newHeight]
        ];
    }

    /**
     * Prétraitement des audios
     */
    private function preprocessAudio($file, $fieldConfig)
    {
        $categoryFolder = $this->getCategoryValue();
        $collecteSlug = Str::slug($this->collecte->nom, '_');

        $rules = $this->collecte->preprocess_rules['audio'] ?? [
            'sample_rate' => 16000,
            'channels' => 1,
            'format' => 'wav'
        ];

        $filename = time() . '_' . Str::random(16) . '.' . $rules['format'];
        $path = 'collectes/' . $collecteSlug . '/audio/' . $categoryFolder . '/' . $filename;

        $fullPath = storage_path('app/public/' . $path);
        $directory = dirname($fullPath);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $ffmpegAvailable = !empty(shell_exec('which ffmpeg'));

        if ($ffmpegAvailable) {
            $tempPath = $file->getRealPath();
            $command = "ffmpeg -i \"{$tempPath}\" -ar {$rules['sample_rate']} -ac {$rules['channels']} -y \"{$fullPath}\" 2>&1";
            shell_exec($command);
        } else {
            $file->storeAs('collectes/' . $collecteSlug . '/audio/' . $categoryFolder . '/', $filename, 'public');
        }

        return [
            'path' => $path,
            'category' => $categoryFolder,
            'filename' => $filename
        ];
    }

    /**
     * Soumission du formulaire - PERMET LA VALEUR 0
     */
    public function submitForm()
    {
        if ($this->collecte->status == 'fermee') {
            session()->flash('error', 'Cette collecte est fermée.');
            return;
        }

        $rules = [];
        foreach ($this->collecte->config_schema as $field) {
            $fieldName = $field['name'];

            // Règle de validation pour les champs requis (sauf fichiers)
            if (($field['required'] ?? false) && !str_starts_with($field['type'], 'file_')) {
                $rules['formData.' . $fieldName] = 'required';
            }

            // Règles pour les fichiers
            if (str_starts_with($field['type'], 'file_')) {
                $maxSize = ($field['max_size'] ?? 5) * 1024;
                if ($field['required'] ?? false) {
                    $rules['files.' . $fieldName] = 'required|file|max:' . $maxSize;
                } else {
                    $rules['files.' . $fieldName] = 'nullable|file|max:' . $maxSize;
                }

                if ($field['type'] == 'file_image') {
                    $rules['files.' . $fieldName] .= '|image|mimes:jpeg,png,jpg,gif,webp';
                }
                if ($field['type'] == 'file_audio') {
                    $rules['files.' . $fieldName] .= '|mimes:mp3,wav,ogg,m4a';
                }
            }
        }

        $this->validate($rules);

        $data = [];
        $processedFiles = [];

        foreach ($this->collecte->config_schema as $field) {
            $fieldName = $field['name'];
            // Récupérer la valeur même si c'est 0 ou "0"
            $value = $this->formData[$fieldName] ?? null;

            // Si c'est une chaîne vide, on la transforme en null sauf si c'est "0"
            if ($value === '' && $value !== '0') {
                $value = null;
            }

            if (str_starts_with($field['type'], 'file_') && isset($this->files[$fieldName]) && $this->files[$fieldName]) {
                $file = $this->files[$fieldName];

                if ($field['type'] == 'file_image') {
                    $processed = $this->preprocessImage($file, $field);
                    $value = $processed['original'];
                    $processedFiles[$fieldName] = $processed;
                } elseif ($field['type'] == 'file_audio') {
                    $processed = $this->preprocessAudio($file, $field);
                    $value = $processed['path'];
                    $processedFiles[$fieldName] = $processed;
                }
            }

            $data[$fieldName] = $value;
        }

        DonneeCollecte::create([
            'collecte_id' => $this->collecte->id,
            'user_id' => Auth::id(),
            'data' => $data,
            'fichiers_processes' => $processedFiles,
            'ip_address' => request()->ip(),
        ]);

        session()->flash('success', 'Données soumises avec succès !');
        $this->formData = [];
        $this->files = [];
    }

    // ==================== GESTION DES DONNÉES ====================

    public function deleteDonnee($donneeId)
    {
        $donnee = DonneeCollecte::findOrFail($donneeId);

        $isCreator = $this->collecte->created_by == Auth::id();
        $isOwner = $donnee->user_id == Auth::id();

        if ($isCreator || $isOwner) {
            if (!empty($donnee->fichiers_processes)) {
                foreach ($donnee->fichiers_processes as $file) {
                    if (isset($file['original']) && Storage::disk('public')->exists($file['original'])) {
                        Storage::disk('public')->delete($file['original']);
                    }
                    if (isset($file['thumbnail']) && Storage::disk('public')->exists($file['thumbnail'])) {
                        Storage::disk('public')->delete($file['thumbnail']);
                    }
                    if (isset($file['path']) && Storage::disk('public')->exists($file['path'])) {
                        Storage::disk('public')->delete($file['path']);
                    }
                }
            }

            $donnee->delete();
            session()->flash('success', 'Donnée supprimée avec succès !');
        } else {
            session()->flash('error', 'Vous n\'avez pas les droits pour supprimer cette donnée.');
        }
    }

    // ==================== GESTION DES LABELS (CONFIGURATION) ====================

    public function editField($index)
    {
        $this->editingFieldIndex = $index;
        $field = $this->labels[$index];

        $this->editField = [
            'name' => $field['name'],
            'label' => $field['label'],
            'type' => $field['type'],
            'required' => $field['required'] ?? false,
            'placeholder' => $field['placeholder'] ?? '',
            'options' => [],
            'option_labels' => [],
        ];

        if (in_array($field['type'], ['select', 'radio']) && isset($field['options'])) {
            foreach ($field['options'] as $value => $label) {
                $this->editField['options'][] = $value;
                $this->editField['option_labels'][] = $label;
            }
        }

        $this->dispatch('open-edit-modal');
    }

    public function updateField()
    {
        $cleanLabel = [
            'name' => $this->editField['name'],
            'label' => $this->editField['label'],
            'type' => $this->editField['type'],
            'required' => $this->editField['required'],
        ];

        if (in_array($this->editField['type'], ['select', 'radio']) && !empty($this->editField['options'])) {
            $options = [];
            foreach ($this->editField['options'] as $index => $value) {
                $options[$value] = $this->editField['option_labels'][$index] ?? $value;
            }
            $cleanLabel['options'] = $options;
        }

        if ($this->editField['placeholder']) $cleanLabel['placeholder'] = $this->editField['placeholder'];

        $this->labels[$this->editingFieldIndex] = $cleanLabel;
        $this->editingFieldIndex = null;
        $this->editField = [
            'name' => '',
            'label' => '',
            'type' => 'text',
            'required' => false,
            'placeholder' => '',
            'options' => [],
            'option_labels' => [],
        ];

        session()->flash('success', 'Champ modifié avec succès !');
    }

    public function cancelEdit()
    {
        $this->editingFieldIndex = null;
        $this->editField = [
            'name' => '',
            'label' => '',
            'type' => 'text',
            'required' => false,
            'placeholder' => '',
            'options' => [],
            'option_labels' => [],
        ];
    }

    public function addEditOption()
    {
        if (!empty($this->tempOptionLabel) && $this->tempOptionValue !== '') {
            $this->editField['options'][] = $this->tempOptionValue;
            $this->editField['option_labels'][] = $this->tempOptionLabel;
            $this->tempOptionLabel = '';
            $this->tempOptionValue = '';
        }
    }

    public function removeEditOption($index)
    {
        unset($this->editField['options'][$index]);
        unset($this->editField['option_labels'][$index]);
        $this->editField['options'] = array_values($this->editField['options']);
        $this->editField['option_labels'] = array_values($this->editField['option_labels']);
    }

    public function addOption()
    {
        if (!empty($this->tempOptionLabel) && $this->tempOptionValue !== '') {
            $this->newLabel['options'][] = $this->tempOptionValue;
            $this->newLabel['option_labels'][] = $this->tempOptionLabel;
            $this->tempOptionLabel = '';
            $this->tempOptionValue = '';
        }
    }

    public function removeOption($index)
    {
        unset($this->newLabel['options'][$index]);
        unset($this->newLabel['option_labels'][$index]);
        $this->newLabel['options'] = array_values($this->newLabel['options']);
        $this->newLabel['option_labels'] = array_values($this->newLabel['option_labels']);
    }

    public function addLabel()
    {
        $cleanLabel = [
            'name' => $this->newLabel['name'],
            'label' => $this->newLabel['label'],
            'type' => $this->newLabel['type'],
            'required' => $this->newLabel['required'],
        ];

        if (in_array($this->newLabel['type'], ['select', 'radio']) && !empty($this->newLabel['options'])) {
            $options = [];
            foreach ($this->newLabel['options'] as $index => $value) {
                $options[$value] = $this->newLabel['option_labels'][$index] ?? $value;
            }
            $cleanLabel['options'] = $options;
        }

        if ($this->newLabel['placeholder']) $cleanLabel['placeholder'] = $this->newLabel['placeholder'];

        $this->labels[] = $cleanLabel;

        $this->newLabel = [
            'name' => '',
            'label' => '',
            'type' => 'text',
            'required' => false,
            'placeholder' => '',
            'options' => [],
            'option_labels' => [],
        ];
    }

    public function removeLabel($index)
    {
        unset($this->labels[$index]);
        $this->labels = array_values($this->labels);
    }

    public function moveLabelUp($index)
    {
        if ($index > 0) {
            $temp = $this->labels[$index];
            $this->labels[$index] = $this->labels[$index - 1];
            $this->labels[$index - 1] = $temp;
        }
    }

    public function moveLabelDown($index)
    {
        if ($index < count($this->labels) - 1) {
            $temp = $this->labels[$index];
            $this->labels[$index] = $this->labels[$index + 1];
            $this->labels[$index + 1] = $temp;
        }
    }

    public function saveLabels()
    {
        $this->collecte->update(['config_schema' => $this->labels]);
        session()->flash('success', 'Formulaire mis à jour !');
        $this->activeTab = 'form';
    }

    // ==================== INVITATIONS ====================

    public function sendInvitation()
    {
        $user = User::where('email', $this->invitationEmail)->first();

        if (!$user) {
            session()->flash('error', 'Utilisateur non trouvé.');
            return;
        }

        $exists = $this->collecte->utilisateurs()->where('user_id', $user->id)->exists();
        if ($exists) {
            session()->flash('error', 'Cet utilisateur fait déjà partie de la collecte');
            return;
        }

        $this->collecte->utilisateurs()->attach($user->id, ['role' => $this->invitationRole]);

        session()->flash('success', 'Utilisateur ajouté !');
        $this->invitationEmail = '';
    }

    public function removeMember($userId)
    {
        if ($this->collecte->created_by == Auth::id()) {
            $this->collecte->utilisateurs()->detach($userId);
            session()->flash('success', 'Membre retiré');
        }
    }

    // ==================== STATS ET DONNÉES ====================

    public function getDonnees()
    {
        return DonneeCollecte::where('collecte_id', $this->collecte->id)
            ->with('utilisateur')
            ->latest()
            ->get();
    }

    public function getStats()
    {
        $donnees = $this->getDonnees();

        $uniqueUsers = DonneeCollecte::where('collecte_id', $this->collecte->id)
            ->distinct('user_id')
            ->count('user_id');

        return [
            'total' => $donnees->count(),
            'users' => $uniqueUsers,
            'last_week' => $donnees->where('created_at', '>=', now()->subDays(7))->count(),
        ];
    }

    public function getTopContributors()
    {
        return DonneeCollecte::where('collecte_id', $this->collecte->id)
            ->select('user_id', DB::raw('count(*) as total'))
            ->with('utilisateur')
            ->groupBy('user_id')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.collecte.show-collecte', [
            'stats' => $this->getStats(),
            'donnees' => $this->getDonnees(),
            'topContributors' => $this->getTopContributors(),
        ])->layout('layouts.app');
    }
}
