<?php

namespace App\Livewire\Collecte;

use Livewire\Component;
use App\Models\Collecte;
use Illuminate\Support\Facades\Auth;

class CreateCollecte extends Component
{
    public $nom = '';
    public $description = '';
    public $status = 'brouillon';

    public $labels = [];
    public $existingSelectFields = [];
    public $categoryFieldIndex = null;

    public $newLabel = [
        'name' => '',
        'label' => '',
        'type' => 'text',
        'required' => false,
        'placeholder' => '',
        'default_value' => '',
        'min' => '',
        'max' => '',
        'options' => [],
        'option_labels' => [],
        'max_size' => 5,
    ];

    public $tempOptionLabel = '';
    public $tempOptionValue = '';

    public function mount()
    {
        $this->updateExistingSelectFields();
    }

    private function updateExistingSelectFields()
    {
        $this->existingSelectFields = [];
        foreach ($this->labels as $index => $field) {
            if (in_array($field['type'], ['select', 'radio'])) {
                $this->existingSelectFields[$index] = $field['label'] . ' (' . $field['name'] . ')';
            }
        }
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
        $this->validate([
            'newLabel.name' => 'required|alpha_dash',
            'newLabel.label' => 'required|string',
        ]);

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
        if ($this->newLabel['default_value']) $cleanLabel['default_value'] = $this->newLabel['default_value'];
        if ($this->newLabel['min'] || $this->newLabel['min'] === '0') $cleanLabel['min'] = $this->newLabel['min'];
        if ($this->newLabel['max'] || $this->newLabel['max'] === '0') $cleanLabel['max'] = $this->newLabel['max'];
        if ($this->newLabel['max_size']) $cleanLabel['max_size'] = $this->newLabel['max_size'];

        $this->labels[] = $cleanLabel;
        $this->updateExistingSelectFields();

        $this->newLabel = [
            'name' => '',
            'label' => '',
            'type' => 'text',
            'required' => false,
            'placeholder' => '',
            'default_value' => '',
            'min' => '',
            'max' => '',
            'options' => [],
            'option_labels' => [],
            'max_size' => 5,
        ];

        $this->tempOptionLabel = '';
        $this->tempOptionValue = '';
    }

    public function removeLabel($index)
    {
        unset($this->labels[$index]);
        $this->labels = array_values($this->labels);
        $this->updateExistingSelectFields();
        if ($this->categoryFieldIndex !== null && $this->categoryFieldIndex >= count($this->labels)) {
            $this->categoryFieldIndex = null;
        }
    }

    public function moveLabelUp($index)
    {
        if ($index > 0) {
            $temp = $this->labels[$index];
            $this->labels[$index] = $this->labels[$index - 1];
            $this->labels[$index - 1] = $temp;
            $this->updateExistingSelectFields();

            if ($this->categoryFieldIndex === $index) {
                $this->categoryFieldIndex = $index - 1;
            } elseif ($this->categoryFieldIndex === $index - 1) {
                $this->categoryFieldIndex = $index;
            }
        }
    }

    public function moveLabelDown($index)
    {
        if ($index < count($this->labels) - 1) {
            $temp = $this->labels[$index];
            $this->labels[$index] = $this->labels[$index + 1];
            $this->labels[$index + 1] = $temp;
            $this->updateExistingSelectFields();

            if ($this->categoryFieldIndex === $index) {
                $this->categoryFieldIndex = $index + 1;
            } elseif ($this->categoryFieldIndex === $index + 1) {
                $this->categoryFieldIndex = $index;
            }
        }
    }

    public function save()
    {
        $this->validate([
            'nom' => 'required|min:3|max:255',
        ]);

        $categoryFieldIndex = $this->categoryFieldIndex;
        if ($categoryFieldIndex === '' || $categoryFieldIndex === 'null') {
            $categoryFieldIndex = null;
        }
        if ($categoryFieldIndex !== null) {
            $categoryFieldIndex = (int) $categoryFieldIndex;
        }

        $collecte = Collecte::create([
            'nom' => $this->nom,
            'description' => $this->description,
            'created_by' => Auth::id(),
            'status' => $this->status,
            'config_schema' => $this->labels,
            'category_field_index' => $categoryFieldIndex,
            'preprocess_rules' => [
                'image' => ['max_width' => 800, 'max_height' => 600, 'format' => 'jpg', 'quality' => 85],
                'audio' => ['sample_rate' => 16000, 'channels' => 1, 'format' => 'wav']
            ]
        ]);

        $collecte->utilisateurs()->attach(Auth::id(), ['role' => 'superviseur']);

        session()->flash('success', 'Collecte créée avec succès !');

        return redirect()->route('collectes.list');
    }

    public function render()
    {
        return view('livewire.collecte.create-collecte')->layout('layouts.app');
    }
}
