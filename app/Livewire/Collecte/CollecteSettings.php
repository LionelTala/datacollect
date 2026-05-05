<?php

namespace App\Livewire\Collecte;

use Livewire\Component;
use App\Models\Collecte;
use Illuminate\Support\Facades\Auth;

class CollecteSettings extends Component
{
    public $collecte;
    public $labels = [];
    public $newLabel = [
        'name' => '', 'label' => '', 'type' => 'text', 'required' => false,
        'placeholder' => '', 'options' => [], 'option_labels' => [],
    ];
    public $tempOptionLabel = '';
    public $tempOptionValue = '';
    public $editingFieldIndex = null;


    public function mount($collecteId)
    {
        $this->collecte = Collecte::findOrFail($collecteId);
        $this->labels = $this->collecte->config_schema;

        if ($this->collecte->created_by != Auth::id() && !Auth::user()->is_admin) {
            abort(403);
        }
    }
    public $editField = [
        'name' => '',
        'label' => '',
        'type' => 'text',
        'required' => false,
        'placeholder' => '',
        'options' => [],
        'option_labels' => [],
    ];
    public function editField($index)
    {
        $field = $this->labels[$index];

        $this->editField = [
            'name' => $field['name'] ?? '',
            'label' => $field['label'] ?? '',
            'type' => $field['type'] ?? 'text',
            'required' => $field['required'] ?? false,
            'placeholder' => $field['placeholder'] ?? '',
            'options' => [],
            'option_labels' => [],
        ];

        if (in_array($this->editField['type'], ['select', 'radio']) && isset($field['options'])) {
            foreach ($field['options'] as $value => $label) {
                $this->editField['options'][] = $value;
                $this->editField['option_labels'][] = $label;
            }
        }

        $this->dispatch('open-edit-modal');
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
        unset($this->newLabel['options'][$index], $this->newLabel['option_labels'][$index]);
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
            foreach ($this->newLabel['options'] as $idx => $value) {
                $options[$value] = $this->newLabel['option_labels'][$idx] ?? $value;
            }
            $cleanLabel['options'] = $options;
        }
        if ($this->newLabel['placeholder']) $cleanLabel['placeholder'] = $this->newLabel['placeholder'];

        $this->labels[] = $cleanLabel;
        $this->newLabel = ['name' => '', 'label' => '', 'type' => 'text', 'required' => false, 'placeholder' => '', 'options' => [], 'option_labels' => []];
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
        session()->flash('success', 'Formulaire mis à jour');
    }

    public function render()
    {
        return view('livewire.collecte.collecte-settings');
    }
}
