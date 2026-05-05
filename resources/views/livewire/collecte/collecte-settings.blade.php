<div>
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-4">
                <i class="fas fa-sliders-h me-2 text-primary"></i>
                Configuration du formulaire
            </h5>

            @if(session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Liste des champs existants -->
            @if(count($labels) > 0)
                <div class="mb-4">
                    <label class="fw-semibold mb-2">📋 Champs actuels :</label>
                    @foreach($labels as $index => $field)
                        <div class="border rounded-3 p-3 mb-2 bg-light">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex gap-2 mb-1 flex-wrap">
                                        <strong>{{ $field['label'] }}</strong>
                                        <span class="badge bg-secondary">{{ $field['type'] }}</span>
                                        @if(isset($field['required']) && $field['required'])
                                            <span class="badge bg-danger">Obligatoire</span>
                                        @endif
                                        @if(isset($field['options']) && count($field['options']) > 0)
                                            <span class="badge bg-info">{{ count($field['options']) }} options</span>
                                        @endif
                                    </div>
                                    <div class="text-muted small"><code>{{ $field['name'] }}</code></div>
                                    @if(isset($field['options']) && count($field['options']) > 0)
                                        <div class="mt-2 d-flex flex-wrap gap-1">
                                            @foreach($field['options'] as $val => $lib)
                                                <span class="badge bg-white border">{{ $lib }} ({{ $val }})</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div class="btn-group btn-group-sm">
                                    <button wire:click="editField({{ $index }})" class="btn btn-outline-secondary rounded-2" title="Modifier" data-bs-toggle="modal" data-bs-target="#editFieldModal">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button wire:click="moveLabelUp({{ $index }})" class="btn btn-outline-secondary rounded-2">↑</button>
                                    <button wire:click="moveLabelDown({{ $index }})" class="btn btn-outline-secondary rounded-2">↓</button>
                                    <button wire:click="removeLabel({{ $index }})" class="btn btn-outline-danger rounded-2">🗑</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info mb-4">Aucun champ pour le moment.</div>
            @endif

            <!-- Ajouter nouveau champ -->
            <div class="border rounded-3 p-3 mt-3" style="background: #f8fafc;">
                <h6 class="fw-bold mb-3"><i class="fas fa-plus-circle me-1 text-primary"></i> Nouveau champ</h6>
                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <input type="text" wire:model="newLabel.name" class="form-control" placeholder="Nom technique (ex: maladie)">
                    </div>
                    <div class="col-md-4">
                        <input type="text" wire:model="newLabel.label" class="form-control" placeholder="Libellé (ex: Maladie)">
                    </div>
                    <div class="col-md-3">
                        <select wire:model="newLabel.type" class="form-select">
                            <option value="text">📝 Texte</option>
                            <option value="textarea">📄 Texte long</option>
                            <option value="number">🔢 Nombre</option>
                            <option value="select">📋 Menu déroulant</option>
                            <option value="radio">🔘 Boutons radio</option>
                            <option value="file_image">🖼️ Image</option>
                            <option value="file_audio">🎵 Audio</option>
                        </select>
                    </div>
                    <div class="col-md-1 d-flex align-items-center">
                        <div class="form-check">
                            <input type="checkbox" wire:model="newLabel.required" class="form-check-input" id="newRequired">
                            <label class="form-check-label small" for="newRequired">Req.</label>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <input type="text" wire:model="newLabel.placeholder" class="form-control" placeholder="Placeholder (optionnel)">
                </div>

                @if(in_array($newLabel['type'], ['select', 'radio']))
                    <div class="mt-2 p-2 bg-white rounded-3 mb-3">
                        <label class="small fw-semibold mb-2">Options :</label>
                        <div class="small text-muted mb-2 p-2 bg-light rounded-2">
                            💡 Exemple: "Paludisme" → stocker "1" en base
                        </div>
                        @if(count($newLabel['options']) > 0)
                            @foreach($newLabel['options'] as $idx => $opt)
                                <div class="d-flex gap-2 mb-1">
                                    <input type="text" class="form-control form-control-sm" value="{{ $newLabel['option_labels'][$idx] }}" placeholder="Libellé" disabled>
                                    <input type="text" class="form-control form-control-sm" value="{{ $opt }}" placeholder="Valeur" disabled>
                                    <button wire:click="removeOption({{ $idx }})" class="btn btn-sm btn-outline-danger">✕</button>
                                </div>
                            @endforeach
                        @else
                            <div class="text-muted small mb-2">Aucune option</div>
                        @endif
                        <div class="d-flex gap-2 mt-2">
                            <input type="text" wire:model="tempOptionLabel" class="form-control form-control-sm" placeholder="Libellé (ex: Paludisme)">
                            <input type="text" wire:model="tempOptionValue" class="form-control form-control-sm" placeholder="Valeur (ex: 1)">
                            <button wire:click="addOption" class="btn btn-sm btn-primary">Ajouter</button>
                        </div>
                    </div>
                @endif

                <button wire:click="addLabel" class="btn btn-primary w-100 mt-3">
                    <i class="fas fa-plus me-2"></i>Ajouter ce champ
                </button>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <button wire:click="saveLabels" class="btn btn-success rounded-3 px-4 py-2">
                    <i class="fas fa-save me-2"></i>Sauvegarder
                </button>
            </div>
        </div>
    </div>

    <!-- Modal d'édition de champ -->
    <div class="modal fade" id="editFieldModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="fw-bold">
                        <i class="fas fa-edit me-2 text-primary"></i>
                        Modifier le champ
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 pb-4">
                    @if(!empty($editField))
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Nom technique</label>
                                <input type="text" wire:model="editField.name" class="form-control rounded-3" readonly disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Libellé</label>
                                <input type="text" wire:model="editField.label" class="form-control rounded-3">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Type</label>
                                <select wire:model="editField.type" class="form-select rounded-3" disabled>
                                    <option value="text">Texte</option>
                                    <option value="textarea">Texte long</option>
                                    <option value="number">Nombre</option>
                                    <option value="select">Menu</option>
                                    <option value="radio">Radio</option>
                                    <option value="file_image">Image</option>
                                    <option value="file_audio">Audio</option>
                                </select>
                            </div>
                            <div class="col-md-6 d-flex align-items-center">
                                <div class="form-check">
                                    <input type="checkbox" wire:model="editField.required" class="form-check-input" id="editRequired">
                                    <label class="form-check-label small" for="editRequired">Obligatoire</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <input type="text" wire:model="editField.placeholder" class="form-control" placeholder="Placeholder">
                            </div>
                        </div>

                        @if(isset($editField['type']) && in_array($editField['type'], ['select', 'radio']))
                            <div class="mt-3 p-3 bg-light rounded-3">
                                <label class="fw-semibold small mb-2">Options</label>
                                @if(!empty($editField['options']))
                                    @foreach($editField['options'] as $idx => $opt)
                                        <div class="d-flex gap-2 mb-2">
                                            <input type="text" class="form-control form-control-sm" value="{{ $editField['option_labels'][$idx] ?? '' }}" disabled>
                                            <input type="text" class="form-control form-control-sm" value="{{ $opt }}" disabled>
                                            <button wire:click="removeEditOption({{ $idx }})" class="btn btn-sm btn-outline-danger">✕</button>
                                        </div>
                                    @endforeach
                                @endif
                                <div class="d-flex gap-2 mt-2">
                                    <input type="text" wire:model="tempOptionLabel" class="form-control form-control-sm" placeholder="Libellé">
                                    <input type="text" wire:model="tempOptionValue" class="form-control form-control-sm" placeholder="Valeur">
                                    <button wire:click="addEditOption" class="btn btn-sm btn-primary">Ajouter</button>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" wire:click="updateField" class="btn btn-primary rounded-3" data-bs-dismiss="modal">Enregistrer</button>
                </div>
            </div>
        </div>
    </div>
</div>
