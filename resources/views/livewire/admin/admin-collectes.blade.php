<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold">📊 Toutes les collectes</h1>
            <p class="text-muted">Gérez l'ensemble des collectes de la plateforme</p>
        </div>
    </div>

    @if(session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filtres -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="row g-2">
                <div class="col-md-8">
                    <input type="text" wire:model.live="search" class="form-control rounded-3" placeholder="🔍 Rechercher par nom ou créateur...">
                </div>
                <div class="col-md-2">
                    <select wire:model.live="perPage" class="form-select rounded-3">
                        <option value="10">10 par page</option>
                        <option value="20">20 par page</option>
                        <option value="50">50 par page</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select wire:model.live="statusFilter" class="form-select rounded-3">
                        <option value="">Tous les statuts</option>
                        <option value="active">✅ Actives</option>
                        <option value="brouillon">📝 Brouillons</option>
                        <option value="fermee">🔒 Fermées</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3">ID</th>
                        <th>Nom</th>
                        <th>Créateur</th>
                        <th>Statut</th>
                        <th>Données</th>
                        <th>Participants</th>
                        <th>Créée le</th>
                        <th class="pe-4 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($collectes as $collecte)
                        @php
                            $donneesCount = \App\Models\DonneeCollecte::where('collecte_id', $collecte->id)->count();
                            $participantsCount = \App\Models\DonneeCollecte::where('collecte_id', $collecte->id)->distinct('user_id')->count();
                        @endphp
                        <tr>
                            <td class="ps-4 py-3">{{ $collecte->id }}</td>
                            <td class="py-3">
                                <strong>{{ $collecte->nom }}</strong><br>
                                <small class="text-muted">{{ Str::limit($collecte->description, 50) }}</small>
                            </td>
                            <td class="py-3">{{ $collecte->createur->name }}</td>
                            <td class="py-3">
                                <select wire:change="changeStatus({{ $collecte->id }}, $event.target.value)"
                                        class="form-select form-select-sm rounded-pill"
                                        style="width: auto;">
                                    <option value="active" {{ $collecte->status == 'active' ? 'selected' : '' }}>✅ Active</option>
                                    <option value="brouillon" {{ $collecte->status == 'brouillon' ? 'selected' : '' }}>📝 Brouillon</option>
                                    <option value="fermee" {{ $collecte->status == 'fermee' ? 'selected' : '' }}>🔒 Fermée</option>
                                </select>
                            </td>
                            <td class="py-3">{{ $donneesCount }}</td>
                            <td class="py-3">{{ $participantsCount }}</td>
                            <td class="py-3">{{ $collecte->created_at->format('d/m/Y') }}</td>
                            <td class="pe-4 py-3 text-end">
                                <a href="{{ route('collecte.show', $collecte->id) }}" class="btn btn-sm btn-outline-primary rounded-3" target="_blank">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button wire:click="delete({{ $collecte->id }})" class="btn btn-sm btn-outline-danger rounded-3" onclick="return confirm('Supprimer cette collecte ?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">Aucune collecte</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            {{ $collectes->links() }}
        </div>
    </div>
</div>
