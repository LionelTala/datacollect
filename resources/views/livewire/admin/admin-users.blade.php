<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold">👥 Utilisateurs</h1>
            <p class="text-muted">Gérez tous les utilisateurs de la plateforme</p>
        </div>
        <button wire:click="$set('showModal', true)" class="btn btn-primary rounded-3">
            <i class="fas fa-plus me-2"></i>Nouvel utilisateur
        </button>
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
                <div class="col-md-6">
                    <input type="text" wire:model.live="search" class="form-control rounded-3" placeholder="🔍 Rechercher...">
                </div>
                <div class="col-md-2">
                    <select wire:model.live="perPage" class="form-select rounded-3">
                        <option value="10">10 par page</option>
                        <option value="20">20 par page</option>
                        <option value="50">50 par page</option>
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
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Inscrit le</th>
                        <th class="pe-4 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="ps-4 py-3">{{ $user->id }}</td>
                            <td class="py-3">{{ $user->name }}</td>
                            <td class="py-3">{{ $user->email }}</td>
                            <td class="py-3">
                                @if($user->is_admin)
                                    <span class="badge bg-primary">👑 Admin</span>
                                @else
                                    <span class="badge bg-secondary">Utilisateur</span>
                                @endif
                            </td>
                            <td class="py-3">{{ $user->created_at->format('d/m/Y') }}</td>
                            <td class="pe-4 py-3 text-end">
                                <button wire:click="edit({{ $user->id }})" class="btn btn-sm btn-outline-secondary rounded-3">
                                    <i class="fas fa-edit"></i>
                                </button>
                                @if($user->id != Auth::id())
                                    <button wire:click="delete({{ $user->id }})" class="btn btn-sm btn-outline-danger rounded-3" onclick="return confirm('Supprimer ?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">Aucun utilisateur</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Modal Ajout/Modification -->
    @if($showModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="fw-bold">{{ $userId ? 'Modifier' : 'Ajouter' }} un utilisateur</h5>
                    <button type="button" class="btn-close" wire:click="$set('showModal', false)"></button>
                </div>
                <div class="modal-body px-4 pb-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nom</label>
                        <input type="text" wire:model="name" class="form-control rounded-3">
                        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" wire:model="email" class="form-control rounded-3">
                        @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ $userId ? 'Nouveau mot de passe (optionnel)' : 'Mot de passe' }}</label>
                        <input type="password" wire:model="password" class="form-control rounded-3">
                        @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="form-check">
                        <input type="checkbox" wire:model="is_admin" class="form-check-input" id="isAdmin">
                        <label class="form-check-label" for="isAdmin">👑 Administrateur</label>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-outline-secondary rounded-3" wire:click="$set('showModal', false)">Annuler</button>
                    <button type="button" class="btn btn-primary rounded-3" wire:click="save">Enregistrer</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
