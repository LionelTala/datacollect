<div>
    <!-- En-tête -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                    <a href="{{ route('collectes.list') }}" class="text-muted text-decoration-none">
                        <i class="fas fa-arrow-left me-1"></i> Retour
                    </a>
                    <span class="text-muted">/</span>
                    <span class="badge rounded-pill px-3 py-2 {{
                        $collecte->status == 'active' ? 'bg-success bg-opacity-10 text-success' :
                        ($collecte->status == 'brouillon' ? 'bg-warning bg-opacity-10 text-warning' : 'bg-secondary bg-opacity-10 text-secondary')
                    }}">
                        <i class="fas {{ $collecte->status == 'active' ? 'fa-play' : ($collecte->status == 'brouillon' ? 'fa-pencil-alt' : 'fa-lock') }} me-1"></i>
                        {{ ucfirst($collecte->status) }}
                    </span>
                    @if($collecte->created_by == Auth::id())
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">
                            <i class="fas fa-crown me-1"></i>Créateur
                        </span>
                    @endif
                </div>
                <h1 class="fw-bold mb-1" style="font-size: 2rem;">{{ $collecte->nom }}</h1>
                <p class="text-muted mb-0">{{ $collecte->description }}</p>
            </div>
            <div class="d-flex gap-2">
                @if($collecte->created_by == Auth::id())
                    <button wire:click="$set('activeTab', 'settings')" class="btn btn-outline-secondary rounded-3">
                        <i class="fas fa-cog me-1"></i> Paramètres
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4 text-center p-3">
                <div class="fw-bold fs-4">{{ $stats['total'] }}</div>
                <small class="text-muted">Données collectées</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4 text-center p-3">
                <div class="fw-bold fs-4">{{ $stats['users'] }}</div>
                <small class="text-muted">Contributeurs</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4 text-center p-3">
                <div class="fw-bold fs-4">{{ $stats['last_week'] }}</div>
                <small class="text-muted">Cette semaine</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4 text-center p-3">
                <div class="fw-bold fs-4">{{ $collecte->created_at->diffForHumans() }}</div>
                <small class="text-muted">Créée le</small>
            </div>
        </div>
    </div>

    <!-- Messages -->
    @if(session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Tabs -->
    <div class="d-flex gap-2 mb-4 border-bottom pb-2 flex-wrap">
        <button wire:click="$set('activeTab', 'form')" class="btn rounded-3 px-4 {{ $activeTab == 'form' ? 'btn-primary' : 'btn-outline-secondary' }}">
            <i class="fas fa-edit me-1"></i> Collecte
        </button>
        <button wire:click="$set('activeTab', 'data')" class="btn rounded-3 px-4 {{ $activeTab == 'data' ? 'btn-primary' : 'btn-outline-secondary' }}">
            <i class="fas fa-table me-1"></i> Données
            <span class="badge bg-secondary ms-1">{{ $stats['total'] }}</span>
        </button>
        @if($this->canSeeData() || $this->canExport())
            <button wire:click="$set('activeTab', 'analyse')" class="btn rounded-3 px-4 {{ $activeTab == 'analyse' ? 'btn-primary' : 'btn-outline-secondary' }}">
                <i class="fas fa-chart-line me-1"></i> Analyse
            </button>
        @endif
        <button wire:click="$set('activeTab', 'members')" class="btn rounded-3 px-4 {{ $activeTab == 'members' ? 'btn-primary' : 'btn-outline-secondary' }}">
            <i class="fas fa-users me-1"></i> Équipe
        </button>
        @if($collecte->created_by == Auth::id())
            <button wire:click="$set('activeTab', 'settings')" class="btn rounded-3 px-4 {{ $activeTab == 'settings' ? 'btn-primary' : 'btn-outline-secondary' }}">
                <i class="fas fa-sliders-h me-1"></i> Config
            </button>
        @endif
    </div>

    <!-- TAB FORMULAIRE -->
    @if($activeTab == 'form')
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                @if($collecte->status == 'fermee')
                    <div class="alert alert-warning rounded-3">
                        <i class="fas fa-lock me-2"></i> Cette collecte est fermée.
                    </div>
                @else
                    <form wire:submit.prevent="submitForm" enctype="multipart/form-data">
                        @foreach($collecte->config_schema as $field)
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    {{ $field['label'] }}
                                    @if($field['required'])<span class="text-danger">*</span>@endif
                                </label>

                                @if($field['type'] == 'text')
                                    <input type="text" wire:model="formData.{{ $field['name'] }}" class="form-control rounded-3">

                                @elseif($field['type'] == 'textarea')
                                    <textarea wire:model="formData.{{ $field['name'] }}" rows="3" class="form-control rounded-3"></textarea>

                                @elseif($field['type'] == 'number')
                                    <input type="number" wire:model="formData.{{ $field['name'] }}" class="form-control rounded-3" step="any">

                                @elseif($field['type'] == 'email')
                                    <input type="email" wire:model="formData.{{ $field['name'] }}" class="form-control rounded-3">

                                @elseif($field['type'] == 'date')
                                    <input type="date" wire:model="formData.{{ $field['name'] }}" class="form-control rounded-3">

                                @elseif($field['type'] == 'select')
                                    <select wire:model="formData.{{ $field['name'] }}" class="form-select rounded-3">
                                        <option value="">-- Sélectionnez --</option>
                                        @foreach($field['options'] ?? [] as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>

                                @elseif($field['type'] == 'radio')
                                    @foreach($field['options'] ?? [] as $value => $label)
                                        <div class="form-check">
                                            <input type="radio" wire:model="formData.{{ $field['name'] }}" value="{{ $value }}" class="form-check-input">
                                            <label class="form-check-label">{{ $label }}</label>
                                        </div>
                                    @endforeach

                                @elseif($field['type'] == 'file_image')
                                    <div class="border rounded-3 p-3 bg-light">
                                        <label class="fw-semibold mb-2 d-block">
                                            <i class="fas fa-image me-2 text-primary"></i>Choisir une image
                                        </label>
                                        <input type="file" wire:model="files.{{ $field['name'] }}" class="form-control rounded-3" accept="image/*">
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Formats: JPG, PNG, GIF, WEBP. Max {{ $field['max_size'] ?? 5 }} MB.
                                            </small>
                                        </div>
                                        @if($files[$field['name']] ?? false)
                                            <div class="mt-3">
                                                <div class="alert alert-success py-2 px-3 mb-2 small rounded-3">
                                                    <i class="fas fa-check-circle me-1"></i>
                                                    {{ $files[$field['name']]->getClientOriginalName() }}
                                                </div>
                                                <img src="{{ $files[$field['name']]->temporaryUrl() }}" class="img-fluid rounded-3 border" style="max-height: 150px;">
                                            </div>
                                        @endif
                                        @error('files.' . $field['name'])
                                            <small class="text-danger d-block mt-2">{{ $message }}</small>
                                        @enderror
                                    </div>

                                @elseif($field['type'] == 'file_audio')
                                    <div class="border rounded-3 p-3 bg-light">
                                        <label class="fw-semibold mb-2 d-block">
                                            <i class="fas fa-headphones me-2 text-primary"></i>Choisir un fichier audio
                                        </label>
                                        <input type="file" wire:model="files.{{ $field['name'] }}" class="form-control rounded-3" accept="audio/*">
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Formats: MP3, WAV, OGG, M4A. Max {{ $field['max_size'] ?? 10 }} MB.
                                            </small>
                                        </div>
                                        @if($files[$field['name']] ?? false)
                                            <div class="mt-3">
                                                <div class="alert alert-success py-2 px-3 mb-2 small rounded-3">
                                                    <i class="fas fa-check-circle me-1"></i>
                                                    {{ $files[$field['name']]->getClientOriginalName() }}
                                                </div>
                                                <audio controls class="mt-2 w-100 rounded-3">
                                                    <source src="{{ $files[$field['name']]->temporaryUrl() }}">
                                                </audio>
                                            </div>
                                        @endif
                                        @error('files.' . $field['name'])
                                            <small class="text-danger d-block mt-2">{{ $message }}</small>
                                        @enderror
                                    </div>
                                @endif

                                <div class="text-muted mt-1" style="font-size: 10px;">{{ $field['name'] }}</div>
                            </div>
                        @endforeach

                        <button type="submit" class="btn btn-primary rounded-3 px-4 py-2">
                            <i class="fas fa-paper-plane me-2"></i>Envoyer
                        </button>
                    </form>
                @endif
            </div>
        </div>
    @endif

    <!-- TAB DONNEES -->
    @if($activeTab == 'data')
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3">Utilisateur</th>
                                <th class="py-3">Date</th>
                                @foreach($collecte->config_schema as $field)
                                    <th class="py-3">{{ $field['label'] }}</th>
                                @endforeach
                                <th class="pe-4 py-3 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($donnees as $donnee)
                                @php
                                    $canDelete = ($collecte->created_by == Auth::id()) || ($donnee->user_id == Auth::id());
                                @endphp
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 12px;">
                                                {{ substr($donnee->utilisateur->name, 0, 2) }}
                                            </div>
                                            {{ $donnee->utilisateur->name }}
                                        </div>
                                    </td>
                                    <td class="py-3">{{ $donnee->created_at->format('d/m/Y H:i') }}</td>
                                    @foreach($collecte->config_schema as $field)
                                        <td class="py-3">
                                            @php $value = $donnee->data[$field['name']] ?? null; @endphp
                                            @if(str_starts_with($field['type'], 'file_') && $value)
                                                <a href="{{ Storage::url($value) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-3">
                                                    <i class="fas {{ $field['type'] == 'file_image' ? 'fa-image' : 'fa-file-audio' }} me-1"></i>
                                                    {{ $field['type'] == 'file_image' ? 'Voir' : 'Écouter' }}
                                                </a>
                                            @else
                                                {{ is_array($value) ? json_encode($value) : $value }}
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="pe-4 py-3 text-end">
                                        @if($canDelete)
                                            <button wire:click="deleteDonnee({{ $donnee->id }})"
                                                    class="btn btn-sm btn-outline-danger rounded-3"
                                                    onclick="return confirm('Supprimer cette donnée ?')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ 3 + count($collecte->config_schema) }}" class="text-center py-5">
                                        <i class="fas fa-inbox fa-3x text-muted mb-2 opacity-25"></i>
                                        <p class="text-muted mb-0">Aucune donnée collectée</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if($topContributors->count() > 0)
            <div class="card border-0 shadow-sm rounded-4 mt-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-trophy me-2 text-warning"></i>
                        Top contributeurs
                    </h5>
                    <div class="d-flex gap-3 flex-wrap">
                        @foreach($topContributors as $contributor)
                            <div class="text-center p-3 bg-light rounded-3">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 50px; height: 50px; font-size: 18px;">
                                    {{ substr($contributor->utilisateur->name, 0, 2) }}
                                </div>
                                <div class="fw-semibold">{{ $contributor->utilisateur->name }}</div>
                                <small class="text-muted">{{ $contributor->total }} données</small>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @endif

    <!-- TAB ANALYSE -->
    @if($activeTab == 'analyse')
        @livewire('analyse.analyse-collecte', ['id' => $collecte->id], key($collecte->id))
    @endif

    <!-- TAB EQUIPE -->
    @if($activeTab == 'members')
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0"><i class="fas fa-users me-2 text-primary"></i>Équipe</h5>
                    @if($collecte->created_by == Auth::id())
                        <button class="btn btn-primary btn-sm rounded-3" data-bs-toggle="modal" data-bs-target="#inviteModal">
                            <i class="fas fa-plus me-1"></i> Inviter
                        </button>
                    @endif
                </div>

                <div class="row g-3">
                    <div class="col-md-6 col-lg-4">
                        <div class="border rounded-3 p-3 bg-light">
                            <div class="fw-bold">{{ $collecte->createur->name }}</div>
                            <div class="text-muted small">{{ $collecte->createur->email }}</div>
                            <span class="badge bg-primary mt-1">Créateur</span>
                        </div>
                    </div>

                    @foreach($collecte->utilisateurs as $member)
                        @if($member->id != $collecte->created_by)
                            <div class="col-md-6 col-lg-4">
                                <div class="border rounded-3 p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-bold">{{ $member->name }}</div>
                                            <div class="text-muted small">{{ $member->email }}</div>
                                            <span class="badge bg-info mt-1">{{ $member->pivot->role }}</span>
                                        </div>
                                        @if($collecte->created_by == Auth::id())
                                            <button wire:click="removeMember({{ $member->id }})" class="btn btn-sm btn-outline-danger rounded-3" onclick="return confirm('Retirer ?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Modal Invitation -->
        <div class="modal fade" id="inviteModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-0 pt-4 px-4">
                        <h5 class="fw-bold">Inviter</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body px-4 pb-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" wire:model="invitationEmail" class="form-control rounded-3" placeholder="utilisateur@email.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Rôle</label>
                            <select wire:model="invitationRole" class="form-select rounded-3">
                                <option value="collecteur">📝 Collecteur</option>
                                <option value="superviseur">👑 Superviseur</option>
                                <option value="analyste">📊 Analyste</option>
                            </select>
                        </div>
                        <button wire:click="sendInvitation" class="btn btn-primary w-100 rounded-3 py-2" data-bs-dismiss="modal">
                            <i class="fas fa-paper-plane me-2"></i>Envoyer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- TAB CONFIGURATION -->
    @if($activeTab == 'settings')
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4"><i class="fas fa-sliders-h me-2 text-primary"></i>Configuration</h5>

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
                                <button wire:click="editField({{ $index }})" class="btn btn-outline-secondary rounded-2" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button wire:click="moveLabelUp({{ $index }})" class="btn btn-outline-secondary rounded-2">↑</button>
                                <button wire:click="moveLabelDown({{ $index }})" class="btn btn-outline-secondary rounded-2">↓</button>
                                <button wire:click="removeLabel({{ $index }})" class="btn btn-outline-danger rounded-2">🗑</button>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="border rounded-3 p-3 mt-3" style="background: #f8fafc;">
                    <h6 class="fw-bold mb-3"><i class="fas fa-plus-circle me-1 text-primary"></i> Nouveau champ</h6>
                    <div class="row g-2 mb-3">
                        <div class="col-md-4">
                            <input type="text" wire:model="newLabel.name" class="form-control" placeholder="Nom technique">
                        </div>
                        <div class="col-md-4">
                            <input type="text" wire:model="newLabel.label" class="form-control" placeholder="Libellé">
                        </div>
                        <div class="col-md-3">
                            <select wire:model="newLabel.type" class="form-select">
                                <option value="text">📝 Texte</option>
                                <option value="textarea">📄 Texte long</option>
                                <option value="number">🔢 Nombre</option>
                                <option value="select">📋 Menu</option>
                                <option value="radio">🔘 Radio</option>
                                <option value="file_image">🖼️ Image</option>
                                <option value="file_audio">🎵 Audio</option>
                            </select>
                        </div>
                        <div class="col-md-1 d-flex align-items-center">
                            <input type="checkbox" wire:model="newLabel.required" class="form-check-input" id="newRequired">
                            <label class="form-check-label ms-1" for="newRequired">Req.</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <input type="text" wire:model="newLabel.placeholder" class="form-control" placeholder="Placeholder">
                    </div>

                    @if(in_array($newLabel['type'], ['select', 'radio']))
                        <div class="mt-2 p-2 bg-white rounded-3 mb-3">
                            <label class="small fw-semibold mb-2">Options :</label>
                            @if(count($newLabel['options']) > 0)
                                @foreach($newLabel['options'] as $idx => $opt)
                                    <div class="d-flex gap-2 mb-1">
                                        <input type="text" class="form-control form-control-sm" value="{{ $newLabel['option_labels'][$idx] }}" disabled>
                                        <input type="text" class="form-control form-control-sm" value="{{ $opt }}" disabled>
                                        <button wire:click="removeOption({{ $idx }})" class="btn btn-sm btn-outline-danger">✕</button>
                                    </div>
                                @endforeach
                            @endif
                            <div class="d-flex gap-2 mt-2">
                                <input type="text" wire:model="tempOptionLabel" class="form-control form-control-sm" placeholder="Libellé (ex: Paludisme)">
                                <input type="text" wire:model="tempOptionValue" class="form-control form-control-sm" placeholder="Valeur (ex: 1)">
                                <button wire:click="addOption" class="btn btn-sm btn-primary">Ajouter</button>
                            </div>
                            <div class="small text-muted mt-2">💡 Exemple: "Paludisme" → stocker "1"</div>
                        </div>
                    @endif

                    <button wire:click="addLabel" class="btn btn-primary w-100 mt-3">
                        <i class="fas fa-plus me-2"></i>Ajouter
                    </button>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button wire:click="saveLabels" class="btn btn-success rounded-3 px-4 py-2">
                        <i class="fas fa-save me-2"></i>Sauvegarder
                    </button>
                </div>
            </div>
        </div>
    @endif

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
                                <option value="select">Menu déroulant</option>
                                <option value="radio">Boutons radio</option>
                                <option value="file_image">Image</option>
                                <option value="file_audio">Audio</option>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="form-check">
                                <input type="checkbox" wire:model="editField.required" class="form-check-input" id="editRequired">
                                <label class="form-check-label" for="editRequired">Champ obligatoire</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Placeholder</label>
                            <input type="text" wire:model="editField.placeholder" class="form-control rounded-3">
                        </div>
                    </div>

                    @if(in_array($editField['type'], ['select', 'radio']))
                        <div class="mt-3 p-3 bg-light rounded-3">
                            <label class="fw-semibold small mb-2">Options</label>
                            @foreach($editField['options'] as $idx => $opt)
                                <div class="d-flex gap-2 mb-2">
                                    <input type="text" class="form-control form-control-sm" value="{{ $editField['option_labels'][$idx] }}" disabled>
                                    <input type="text" class="form-control form-control-sm" value="{{ $opt }}" disabled>
                                    <button wire:click="removeEditOption({{ $idx }})" class="btn btn-sm btn-outline-danger">✕</button>
                                </div>
                            @endforeach
                            <div class="row g-2 mt-2">
                                <div class="col-md-5">
                                    <input type="text" wire:model="tempOptionLabel" class="form-control form-control-sm" placeholder="Libellé">
                                </div>
                                <div class="col-md-5">
                                    <input type="text" wire:model="tempOptionValue" class="form-control form-control-sm" placeholder="Valeur">
                                </div>
                                <div class="col-md-2">
                                    <button wire:click="addEditOption" class="btn btn-primary btn-sm w-100">Ajouter</button>
                                </div>
                            </div>
                        </div>
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

<script>
    document.addEventListener('livewire:load', function () {
        Livewire.on('open-edit-modal', () => {
            var myModal = new bootstrap.Modal(document.getElementById('editFieldModal'));
            myModal.show();
        });
    });
</script>
