<div class="py-4">
    <div class="mb-4">
        <div class="d-flex align-items-center gap-3 mb-2">
            <div class="rounded-3 p-2" style="background: linear-gradient(135deg, #4361ee, #818cf8); width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-database text-white"></i>
            </div>
            <div>
                <h1 class="fw-bold mb-0" style="font-size: 1.75rem;">Créer une collecte</h1>
                <p class="text-muted mb-0">Construisez votre formulaire de collecte de données</p>
            </div>
        </div>
    </div>

    @if(session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 border-0 shadow-sm">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form wire:submit.prevent="save">
        <div class="row g-4">
            <div class="col-lg-7">
                <!-- Carte informations générales -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="fas fa-info-circle text-primary"></i>
                            <h5 class="fw-bold mb-0">Informations générales</h5>
                        </div>
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label fw-semibold small text-uppercase text-muted">Nom de la collecte <span class="text-danger">*</span></label>
                                <input type="text" wire:model="nom" class="form-control rounded-3 @error('nom') is-invalid @enderror" placeholder="Ex: Enquête satisfaction 2024">
                                @error('nom') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold small text-uppercase text-muted">Statut</label>
                                <select wire:model="status" class="form-select rounded-3">
                                    <option value="brouillon">📝 Brouillon</option>
                                    <option value="active">✅ Active</option>
                                    <option value="fermee">🔒 Fermée</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small text-uppercase text-muted">Description</label>
                                <textarea wire:model="description" rows="3" class="form-control rounded-3" placeholder="Décrivez l'objectif de cette collecte..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Carte champs existants -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-list text-primary"></i>
                                <h5 class="fw-bold mb-0">Champs du formulaire</h5>
                            </div>
                            @if(count($labels) > 0)
                                <span class="badge bg-primary rounded-pill">{{ count($labels) }}</span>
                            @endif
                        </div>

                        @if(count($labels) > 0)
                            @foreach($labels as $index => $field)
                                <div class="border rounded-3 p-3 mb-2" style="background: #f8fafc;">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                                <strong>{{ $field['label'] }}</strong>
                                                <span class="badge" style="background: #eef2ff; color: #4361ee;">{{ $field['type'] }}</span>
                                                @if(isset($field['required']) && $field['required'])
                                                    <span class="badge" style="background: #fee2e2; color: #dc2626;">Obligatoire</span>
                                                @endif
                                                @if(isset($field['options']) && count($field['options']) > 0)
                                                    <span class="badge" style="background: #dcfce7; color: #16a34a;">{{ count($field['options']) }} options</span>
                                                @endif
                                            </div>
                                            <div class="text-muted small"><code>{{ $field['name'] }}</code></div>
                                            @if(isset($field['options']) && count($field['options']) > 0)
                                                <div class="mt-2 d-flex flex-wrap gap-2">
                                                    @foreach($field['options'] as $val => $lib)
                                                        <span class="badge bg-white border px-3 py-2 rounded-2">
                                                            {{ $lib }} <span class="text-muted fw-normal">({{ $val }})</span>
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" wire:click="moveLabelUp({{ $index }})" class="btn btn-outline-secondary rounded-2" {{ $index == 0 ? 'disabled' : '' }}>
                                                <i class="fas fa-arrow-up"></i>
                                            </button>
                                            <button type="button" wire:click="moveLabelDown({{ $index }})" class="btn btn-outline-secondary rounded-2" {{ $index == count($labels)-1 ? 'disabled' : '' }}>
                                                <i class="fas fa-arrow-down"></i>
                                            </button>
                                            <button type="button" wire:click="removeLabel({{ $index }})" class="btn btn-outline-danger rounded-2">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-2 opacity-25"></i>
                                <p class="text-muted mb-0">Aucun champ pour le moment</p>
                                <small class="text-muted">Créez votre premier champ ci-dessous</small>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Carte ajout nouveau champ -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="fas fa-plus-circle text-primary"></i>
                            <h5 class="fw-bold mb-0">Nouveau champ</h5>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-uppercase text-muted">Nom technique</label>
                                <input type="text" wire:model="newLabel.name" class="form-control rounded-3 @error('newLabel.name') is-invalid @enderror" placeholder="ex: maladie">
                                @error('newLabel.name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-uppercase text-muted">Libellé</label>
                                <input type="text" wire:model="newLabel.label" class="form-control rounded-3 @error('newLabel.label') is-invalid @enderror" placeholder="ex: Maladie">
                                @error('newLabel.label') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- Types de champ -->
                        <div class="mt-3">
                            <label class="form-label fw-semibold small text-uppercase text-muted mb-2">Type de champ</label>
                            <div class="row g-2">
                                @foreach([
                                    ['text', 'fa-font', 'Texte'],
                                    ['textarea', 'fa-align-left', 'Texte long'],
                                    ['number', 'fa-hashtag', 'Nombre'],
                                    ['email', 'fa-envelope', 'Email'],
                                    ['date', 'fa-calendar', 'Date'],
                                    ['select', 'fa-caret-down', 'Menu déroulant'],
                                    ['radio', 'fa-dot-circle', 'Radio'],
                                    ['file_image', 'fa-image', 'Image'],
                                    ['file_audio', 'fa-headphones', 'Audio'],
                                ] as [$type, $icon, $label])
                                    <div class="col-4 col-md-3">
                                        <div wire:click="$set('newLabel.type', '{{ $type }}')"
                                             class="border rounded-3 p-2 text-center transition-all"
                                             style="cursor: pointer; {{ $newLabel['type'] == $type ? 'background: #eef2ff; border-color: #4361ee;' : 'background: white;' }}">
                                            <i class="fas {{ $icon }} {{ $newLabel['type'] == $type ? 'text-primary' : 'text-secondary' }}"></i>
                                            <div class="small {{ $newLabel['type'] == $type ? 'text-primary fw-semibold' : 'text-muted' }}">{{ $label }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Obligatoire -->
                        <div class="form-check mt-3">
                            <input type="checkbox" wire:model="newLabel.required" class="form-check-input" id="required">
                            <label class="form-check-label" for="required">Champ obligatoire</label>
                        </div>

                        <!-- Placeholder -->
                        @if(in_array($newLabel['type'], ['text', 'textarea', 'email']))
                            <div class="mt-3">
                                <label class="form-label fw-semibold small text-uppercase text-muted">Placeholder</label>
                                <input type="text" wire:model="newLabel.placeholder" class="form-control rounded-3" placeholder="Ex: Entrez votre réponse">
                            </div>
                        @endif

                        <!-- Min/Max pour nombre -->
                        @if($newLabel['type'] == 'number')
                            <div class="row g-3 mt-1">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-uppercase text-muted">Valeur min</label>
                                    <input type="number" wire:model="newLabel.min" class="form-control rounded-3" placeholder="0">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-uppercase text-muted">Valeur max</label>
                                    <input type="number" wire:model="newLabel.max" class="form-control rounded-3" placeholder="100">
                                </div>
                            </div>
                        @endif

                        <!-- Options pour select/radio -->
                        @if(in_array($newLabel['type'], ['select', 'radio']))
                            <div class="mt-3 p-3 rounded-3" style="background: #f8fafc;">
                                <label class="fw-semibold small text-uppercase text-muted mb-2">
                                    <i class="fas fa-list me-1"></i>Options
                                </label>

                                <div class="small text-muted mb-2 p-2 bg-white rounded-2 border">
                                    <i class="fas fa-lightbulb text-warning me-1"></i>
                                    <strong>Exemple :</strong> "Masculin" → stocker "0" ou "1" en base
                                </div>

                                @if(count($newLabel['options']) > 0)
                                    <div class="mb-2">
                                        @foreach($newLabel['options'] as $index => $option)
                                            <div class="d-flex gap-2 mb-2">
                                                <div class="flex-grow-1 border rounded-2 bg-white p-2">
                                                    <span class="fw-semibold">{{ $newLabel['option_labels'][$index] }}</span>
                                                    <span class="text-muted ms-2">(stocké: {{ $option }})</span>
                                                </div>
                                                <button type="button" wire:click="removeOption({{ $index }})" class="btn btn-sm btn-outline-danger rounded-2">✕</button>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="alert alert-light text-center py-2 small border">Aucune option</div>
                                @endif

                                <div class="row g-2 mt-2">
                                    <div class="col-md-5">
                                        <input type="text" wire:model="tempOptionLabel" class="form-control form-control-sm rounded-2" placeholder="Ce que l'utilisateur voit">
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" wire:model="tempOptionValue" class="form-control form-control-sm rounded-2" placeholder="Valeur stockée en BD (0/1)">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" wire:click="addOption" class="btn btn-primary btn-sm w-100 rounded-2">Ajouter</button>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Taille fichier -->
                        @if(str_starts_with($newLabel['type'], 'file_'))
                            <div class="mt-3">
                                <label class="form-label fw-semibold small text-uppercase text-muted">Taille max (MB)</label>
                                <input type="number" wire:model="newLabel.max_size" class="form-control rounded-3" style="width: 150px;" placeholder="5">
                            </div>
                        @endif

                        <button type="button" wire:click="addLabel" class="btn btn-primary w-100 mt-4 rounded-3 py-2">
                            <i class="fas fa-plus me-2"></i>Ajouter ce champ
                        </button>
                    </div>
                </div>
            </div>

            <!-- Colonne droite : Aperçu -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 20px;">
                    <div class="card-header bg-primary text-white rounded-top-4 p-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-eye"></i>
                            <h5 class="fw-bold mb-0">Aperçu</h5>
                        </div>
                    </div>
                    <div class="card-body p-4" style="max-height: 60vh; overflow-y: auto;">
                        @if(count($labels) == 0)
                            <div class="text-center py-5">
                                <i class="fas fa-magic fa-3x text-muted mb-3 opacity-25"></i>
                                <p class="text-muted mb-0">Ajoutez des champs</p>
                                <small class="text-muted">pour voir l'aperçu</small>
                            </div>
                        @else
                            @foreach($labels as $field)
                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">
                                        {{ $field['label'] }}
                                        @if(isset($field['required']) && $field['required'])<span class="text-danger">*</span>@endif
                                    </label>

                                    @if($field['type'] == 'text')
                                        <input type="text" class="form-control bg-light border-0" placeholder="{{ $field['placeholder'] ?? 'Réponse...' }}" disabled>
                                    @elseif($field['type'] == 'textarea')
                                        <textarea class="form-control bg-light border-0" rows="2" placeholder="{{ $field['placeholder'] ?? 'Réponse...' }}" disabled></textarea>
                                    @elseif($field['type'] == 'number')
                                        <input type="number" class="form-control bg-light border-0" placeholder="0" disabled>
                                    @elseif($field['type'] == 'email')
                                        <input type="email" class="form-control bg-light border-0" placeholder="email@exemple.com" disabled>
                                    @elseif($field['type'] == 'date')
                                        <input type="date" class="form-control bg-light border-0" disabled>
                                    @elseif($field['type'] == 'select')
                                        <select class="form-select bg-light border-0" disabled>
                                            <option>-- Sélectionnez --</option>
                                            @foreach($field['options'] ?? [] as $lib)
                                                <option>{{ $lib }}</option>
                                            @endforeach
                                        </select>
                                    @elseif($field['type'] == 'radio')
                                        @foreach($field['options'] ?? [] as $lib)
                                            <div class="form-check">
                                                <input type="radio" class="form-check-input" disabled>
                                                <label class="form-check-label small">{{ $lib }}</label>
                                            </div>
                                        @endforeach
                                    @elseif(str_starts_with($field['type'], 'file_'))
                                        <div class="border rounded-3 p-3 text-center bg-light">
                                            <i class="fas fa-cloud-upload-alt fa-2x text-muted"></i>
                                            <p class="small text-muted mb-0">Télécharger un fichier</p>
                                        </div>
                                    @endif

                                    <div class="text-muted mt-1" style="font-size: 10px;">{{ $field['name'] }}</div>
                                </div>
                                @if(!$loop->last)<hr class="my-2">@endif
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION CHOIX DE LA CATÉGORIE POUR LE CLASSEMENT DES FICHIERS -->
        @if(count($existingSelectFields) > 0)
            <div class="card border-0 shadow-sm rounded-4 mt-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="fas fa-tags text-primary"></i>
                        <h5 class="fw-bold mb-0">Classement des fichiers</h5>
                    </div>
                    <div class="text-muted small mb-3">
                        <i class="fas fa-info-circle me-1"></i>
                        Choisissez un champ (select/radio) pour organiser automatiquement les images et audios dans des sous-dossiers.
                    </div>

                    <label class="form-label fw-semibold small text-uppercase text-muted">Organiser par</label>
                    <select wire:model="categoryFieldIndex" class="form-select rounded-3">
                        <option value="">-- Aucun classement --</option>
                        @foreach($existingSelectFields as $index => $label)
                            <option value="{{ $index }}">📁 {{ $label }}</option>
                        @endforeach
                    </select>

                    @if($categoryFieldIndex !== null && isset($labels[$categoryFieldIndex]))
                        <div class="alert alert-info mt-3 py-2 small">
                            <i class="fas fa-folder-tree me-1"></i>
                            Les fichiers seront organisés ainsi :
                            <code>collectes/nom_collecte/images/{{ $labels[$categoryFieldIndex]['name'] }}/[valeur]/</code>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
            <div class="text-muted small">{{ count($labels) }} champ(s)</div>
            <div class="d-flex gap-3">
                <a href="{{ route('collectes.list') }}" class="btn btn-outline-secondary rounded-3 px-4">Annuler</a>
                <button type="submit" class="btn btn-primary rounded-3 px-4" {{ count($labels) == 0 ? 'disabled' : '' }}>
                    <i class="fas fa-save me-2"></i>Créer la collecte
                </button>
            </div>
        </div>
    </form>
</div>

@push('styles')
<style>
    .transition-all {
        transition: all 0.2s ease;
    }
    .form-control:focus, .form-select:focus {
        border-color: #4361ee;
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
    }
    .btn-outline-secondary {
        border: 1px solid #e2e8f0;
        background: white;
        color: #475569;
        transition: all 0.2s;
    }
    .btn-outline-secondary:hover:not(:disabled) {
        background: #f8fafc;
        border-color: #cbd5e1;
        transform: translateY(-1px);
    }
    .btn-primary {
        background: linear-gradient(135deg, #4361ee, #818cf8);
        border: none;
        transition: all 0.2s;
    }
    .btn-primary:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(67, 97, 238, 0.3);
    }
    .btn-primary:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>
@endpush
