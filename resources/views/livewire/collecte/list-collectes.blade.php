<div>
    <!-- En-tête -->
    <div class="mb-4">
        <div class="d-flex align-items-center gap-3 mb-2">
            <div class="rounded-3 p-2" style="background: linear-gradient(135deg, #4361ee, #818cf8); width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-folder-open text-white"></i>
            </div>
            <div>
                <h1 class="fw-bold mb-0" style="font-size: 1.75rem;">Mes collectes</h1>
                <p class="text-muted mb-0">Gérez et consultez vos collectes de données</p>
            </div>
        </div>
    </div>

    <!-- Messages flash -->
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

    <!-- Filtres -->
    <div class="d-flex gap-2 mb-4 flex-wrap">
        <button wire:click="$set('filter', 'all')"
                class="btn rounded-3 px-4 py-2 {{ $filter == 'all' ? 'btn-primary' : 'btn-outline-secondary' }}"
                style="transition: all 0.2s ease;">
            <i class="fas fa-globe me-2"></i>Toutes
            <span class="badge bg-white text-dark ms-2 rounded-pill" style="font-size: 0.7rem;">{{ $totalAll }}</span>
        </button>
        <button wire:click="$set('filter', 'created')"
                class="btn rounded-3 px-4 py-2 {{ $filter == 'created' ? 'btn-primary' : 'btn-outline-secondary' }}"
                style="transition: all 0.2s ease;">
            <i class="fas fa-user me-2"></i>Créées par moi
            <span class="badge bg-white text-dark ms-2 rounded-pill" style="font-size: 0.7rem;">{{ $totalCreated }}</span>
        </button>
        <button wire:click="$set('filter', 'invited')"
                class="btn rounded-3 px-4 py-2 {{ $filter == 'invited' ? 'btn-primary' : 'btn-outline-secondary' }}"
                style="transition: all 0.2s ease;">
            <i class="fas fa-users me-2"></i>Invité(e)
            <span class="badge bg-white text-dark ms-2 rounded-pill" style="font-size: 0.7rem;">{{ $totalInvited }}</span>
        </button>
    </div>

    <!-- Liste des collectes -->
    @if($collectes->count() > 0)
        <div class="row g-4">
            @foreach($collectes as $collecte)
                @php
                    $donneesCount = \App\Models\DonneeCollecte::where('collecte_id', $collecte->id)->count();
                    $participantsCount = \App\Models\DonneeCollecte::where('collecte_id', $collecte->id)->distinct('user_id')->count('user_id');
                @endphp
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 hover-card overflow-hidden">
                        <!-- Bande colorée selon statut -->
                        <div class="position-absolute top-0 start-0 w-100" style="height: 4px;">
                            <div class="w-100 h-100" style="background: {{
                                $collecte->status == 'active' ? 'linear-gradient(90deg, #10b981, #34d399)' :
                                ($collecte->status == 'brouillon' ? 'linear-gradient(90deg, #f59e0b, #fbbf24)' :
                                'linear-gradient(90deg, #6b7280, #9ca3af)')
                            }};"></div>
                        </div>

                        <div class="card-body p-4 pt-5">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex gap-2">
                                    <span class="badge rounded-pill px-3 py-2 {{
                                        $collecte->status == 'active' ? 'bg-success bg-opacity-10 text-success border border-success border-opacity-25' :
                                        ($collecte->status == 'brouillon' ? 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25' :
                                        'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25')
                                    }}" style="font-weight: 500;">
                                        <i class="fas {{
                                            $collecte->status == 'active' ? 'fa-play' :
                                            ($collecte->status == 'brouillon' ? 'fa-pencil-alt' : 'fa-lock')
                                        }} me-1"></i>
                                        {{ ucfirst($collecte->status) }}
                                    </span>
                                </div>

                                @if($collecte->created_by == Auth::id())
                                    <span class="badge rounded-pill px-3 py-2 bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">
                                        <i class="fas fa-crown me-1"></i>Créateur
                                    </span>
                                @else
                                    <span class="badge rounded-pill px-3 py-2 bg-info bg-opacity-10 text-info border border-info border-opacity-25">
                                        <i class="fas fa-user-check me-1"></i>Invité
                                    </span>
                                @endif
                            </div>

                            <a href="{{ route('collecte.show', $collecte->id) }}" class="text-decoration-none">
                                <h5 class="fw-bold mb-2" style="line-height: 1.3; color: #1e293b;">{{ $collecte->nom }}</h5>
                            </a>
                            <p class="text-muted small mb-3" style="min-height: 60px;">
                                {{ Str::limit($collecte->description, 80) ?: 'Aucune description' }}
                            </p>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center text-muted small">
                                    <div>
                                        <i class="fas fa-calendar-alt me-1 text-primary"></i>
                                        {{ $collecte->created_at->format('d/m/Y') }}
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                                            <i class="fas fa-user fa-xs text-secondary"></i>
                                        </div>
                                        <span class="fw-semibold">{{ $collecte->createur->name }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Stats dynamiques -->
                            <div class="border-top pt-3 mt-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex gap-3">
                                        <div class="text-center">
                                            <span class="text-muted small">📊 Données</span>
                                            <div class="fw-bold small text-primary">{{ $donneesCount }}</div>
                                        </div>
                                        <div class="text-center">
                                            <span class="text-muted small">👥 Participants</span>
                                            <div class="fw-bold small text-success">{{ $participantsCount }}</div>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary rounded-circle p-2" style="width: 32px; height: 32px;" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm rounded-3">
                                            <li>
                                                <a href="{{ route('collecte.show', $collecte->id) }}" class="dropdown-item">
                                                    <i class="fas fa-chart-line text-primary me-2"></i>Voir les données
                                                </a>
                                            </li>
                                            <li>
                                                <button wire:click="exportCollecte({{ $collecte->id }})" class="dropdown-item">
                                                    <i class="fas fa-download text-success me-2"></i>Exporter (CSV)
                                                </button>
                                            </li>
                                            @if($collecte->created_by == Auth::id())
                                                <li>
                                                    <button wire:click="openManageParticipants({{ $collecte->id }})" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#participantsModal">
                                                        <i class="fas fa-users text-info me-2"></i>Gérer les participants
                                                    </button>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>

                                                <!-- Actions selon le statut -->
                                                @if($collecte->status == 'active')
                                                    <li>
                                                        <button wire:click="mettreEnBrouillon({{ $collecte->id }})" class="dropdown-item text-warning" onclick="return confirm('Mettre en brouillon ?')">
                                                            <i class="fas fa-pencil-alt me-2"></i>Mettre en brouillon
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button wire:click="archiveCollecte({{ $collecte->id }})" class="dropdown-item text-danger" onclick="return confirm('Archiver cette collecte ?')">
                                                            <i class="fas fa-archive me-2"></i>Archiver
                                                        </button>
                                                    </li>
                                                @elseif($collecte->status == 'brouillon')
                                                    <li>
                                                        <button wire:click="reactiverCollecte({{ $collecte->id }})" class="dropdown-item text-success" onclick="return confirm('Activer cette collecte ?')">
                                                            <i class="fas fa-play me-2"></i>Activer
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button wire:click="archiveCollecte({{ $collecte->id }})" class="dropdown-item text-danger" onclick="return confirm('Archiver cette collecte ?')">
                                                            <i class="fas fa-archive me-2"></i>Archiver
                                                        </button>
                                                    </li>
                                                @elseif($collecte->status == 'fermee')
                                                    <li>
                                                        <button wire:click="reactiverCollecte({{ $collecte->id }})" class="dropdown-item text-success" onclick="return confirm('Réactiver cette collecte ?')">
                                                            <i class="fas fa-play me-2"></i>Réactiver
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button wire:click="mettreEnBrouillon({{ $collecte->id }})" class="dropdown-item text-warning" onclick="return confirm('Mettre en brouillon ?')">
                                                            <i class="fas fa-pencil-alt me-2"></i>Mettre en brouillon
                                                        </button>
                                                    </li>
                                                @endif
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card border-0 shadow-sm rounded-4 text-center py-5">
            <div class="card-body">
                <div class="mb-4">
                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 80px; height: 80px;">
                        <i class="fas fa-inbox fa-3x text-muted opacity-50"></i>
                    </div>
                </div>
                <h4 class="text-muted mb-2">Aucune collecte</h4>
                <p class="text-muted mb-4">Commencez par créer votre première collecte</p>
                <a href="{{ route('collecte.create') }}" class="btn btn-primary rounded-3 px-4 py-2">
                    <i class="fas fa-plus me-2"></i>Créer une collecte
                </a>
            </div>
        </div>
    @endif

    <!-- Modal Gestion des participants -->
    <div class="modal fade" id="participantsModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="fw-bold">
                        <i class="fas fa-users me-2 text-primary"></i>
                        Gérer les participants - {{ $currentCollecteNom }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 pb-4">
                    <!-- Formulaire d'ajout -->
                    <div class="bg-light rounded-3 p-3 mb-4">
                        <h6 class="fw-bold mb-3">➕ Ajouter un participant</h6>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <input type="email" wire:model="invitationEmail" class="form-control rounded-3" placeholder="Email de l'utilisateur">
                                @error('invitationEmail') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-3">
                                <select wire:model="invitationRole" class="form-select rounded-3">
                                    <option value="collecteur">📝 Collecteur</option>
                                    <option value="superviseur">👑 Superviseur</option>
                                    <option value="analyste">📊 Analyste</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button wire:click="addParticipant" class="btn btn-primary w-100 rounded-3">
                                    <i class="fas fa-plus me-1"></i> Ajouter
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Liste des participants -->
                    <div class="mb-3">
                        <h6 class="fw-bold mb-3">📋 Participants actuels ({{ count($members) }})</h6>
                        <div class="border rounded-3 overflow-hidden">
                            <table class="table table-sm mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Nom</th>
                                        <th>Email</th>
                                        <th>Rôle</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($members as $member)
                                        <tr>
                                            <td class="align-middle">{{ $member->name }}</td>
                                            <td class="align-middle">{{ $member->email }}</td>
                                            <td class="align-middle">
                                                <select wire:change="changeRole({{ $member->id }}, $event.target.value)" class="form-select form-select-sm" style="width: auto;">
                                                    <option value="collecteur" {{ $member->pivot->role == 'collecteur' ? 'selected' : '' }}>📝 Collecteur</option>
                                                    <option value="superviseur" {{ $member->pivot->role == 'superviseur' ? 'selected' : '' }}>👑 Superviseur</option>
                                                    <option value="analyste" {{ $member->pivot->role == 'analyste' ? 'selected' : '' }}>📊 Analyste</option>
                                                </select>
                                            </td>
                                            <td class="text-end">
                                                @if($member->id != Auth::id())
                                                    <button wire:click="removeParticipant({{ $member->id }})" class="btn btn-sm btn-outline-danger rounded-3" onclick="return confirm('Retirer ce participant ?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @else
                                                    <span class="badge bg-secondary">Vous</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-3 text-muted">
                                                Aucun participant pour le moment
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-outline-secondary rounded-3 px-4" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .hover-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }

    .hover-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 35px -12px rgba(0, 0, 0, 0.2) !important;
    }

    .btn-outline-secondary {
        border: 1px solid #e2e8f0;
        background: white;
        color: #475569;
        transition: all 0.2s;
    }

    .btn-outline-secondary:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        transform: translateY(-2px);
    }

    .btn-primary {
        background: linear-gradient(135deg, #4361ee, #818cf8);
        border: none;
        transition: all 0.2s;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(67, 97, 238, 0.3);
    }

    .dropdown-item {
        transition: all 0.15s;
        padding: 8px 16px;
        font-size: 0.85rem;
        cursor: pointer;
    }

    .dropdown-item:hover {
        background: #f8fafc;
        padding-left: 20px;
    }

    .badge {
        font-weight: 500;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('livewire:load', function () {
        Livewire.on('open-modal', (modalId) => {
            var myModal = new bootstrap.Modal(document.getElementById(modalId));
            myModal.show();
        });
    });
</script>
@endpush
