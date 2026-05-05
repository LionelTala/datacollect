<?php

namespace App\Livewire\Collecte;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Collecte;
use App\Models\DonneeCollecte;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CollecteForm extends Component
{
    use WithFileUploads;

    public $collecte;
    public $formData = [];
    public $files = [];

    public function mount($collecteId)
    {
        $this->collecte = Collecte::findOrFail($collecteId);
    }

    private function getCategoryField()
    {
        $categoryIndex = $this->collecte->category_field_index;
        if ($categoryIndex !== null && isset($this->collecte->config_schema[$categoryIndex])) {
            return $this->collecte->config_schema[$categoryIndex];
        }
        return null;
    }

    private function getCategoryValue()
    {
        $categoryField = $this->getCategoryField();
        if ($categoryField) {
            $value = $this->formData[$categoryField['name']] ?? 'non_classe';
            if (is_array($value)) $value = reset($value);
            if ($value === null || $value === '') return 'non_classe';
            return Str::slug((string)$value, '_');
        }
        return 'non_classe';
    }

    public function submit()
    {
        if ($this->collecte->status == 'fermee') {
            session()->flash('error', 'Cette collecte est fermée.');
            return;
        }

        $rules = [];
        foreach ($this->collecte->config_schema as $field) {
            if (($field['required'] ?? false) && !str_starts_with($field['type'], 'file_')) {
                $rules['formData.' . $field['name']] = 'required';
            }

            if (str_starts_with($field['type'], 'file_')) {
                $maxSize = ($field['max_size'] ?? 5) * 1024;
                $rules['files.' . $field['name']] = ($field['required'] ?? false) ? 'required|file|max:' . $maxSize : 'nullable|file|max:' . $maxSize;
                if ($field['type'] == 'file_image') $rules['files.' . $field['name']] .= '|image|mimes:jpeg,png,jpg,gif,webp';
                if ($field['type'] == 'file_audio') $rules['files.' . $field['name']] .= '|mimes:mp3,wav,ogg,m4a';
            }
        }

        $this->validate($rules);

        $data = [];
        foreach ($this->collecte->config_schema as $field) {
            $value = $this->formData[$field['name']] ?? null;
            if ($value === '' && $value !== '0') $value = null;

            // Stockage direct du fichier compressé (déjà traité par JS)
            if (str_starts_with($field['type'], 'file_') && isset($this->files[$field['name']]) && $this->files[$field['name']]) {
                $file = $this->files[$field['name']];
                $categoryFolder = $this->getCategoryValue();
                $collecteSlug = Str::slug($this->collecte->nom, '_');

                if ($field['type'] == 'file_image') {
                    $filename = date('Ymd_His') . '_' . Str::random(8) . '.jpg';
                    $path = 'collectes/' . $collecteSlug . '/images/' . $categoryFolder . '/' . $filename;
                    $file->storeAs(dirname($path), $filename, 'public');
                    $value = $path;
                } elseif ($field['type'] == 'file_audio') {
                    $filename = time() . '_' . Str::random(16) . '.' . $file->getClientOriginalExtension();
                    $path = 'collectes/' . $collecteSlug . '/audio/' . $categoryFolder . '/' . $filename;
                    $file->storeAs(dirname($path), $filename, 'public');
                    $value = $path;
                }
            }
            $data[$field['name']] = $value;
        }

        DonneeCollecte::create([
            'collecte_id' => $this->collecte->id,
            'user_id' => Auth::id(),
            'data' => $data,
            'ip_address' => request()->ip(),
        ]);

        session()->flash('success', 'Données soumises avec succès !');
        $this->formData = [];
        $this->files = [];

        return redirect()->route('collecte.show', $this->collecte->id);
    }

    public function render()
    {
        return view('livewire.collecte.collecte-form');
    }
}
