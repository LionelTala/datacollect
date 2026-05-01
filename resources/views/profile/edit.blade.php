<div>
    @if(session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4">
            <i class="fas fa-check-circle me-2"></i>{{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <h1 class="fw-bold mb-4">Mon profil</h1>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card-modern card text-center p-4">
                <div class="position-relative d-inline-block mx-auto mb-3">
                    @if($avatar)
                        <img src="{{ Storage::url($avatar) }}" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                    @else
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 100px; height: 100px; background: linear-gradient(135deg, var(--primary), #818cf8);">
                            <i class="fas fa-user fa-3x text-white"></i>
                        </div>
                    @endif
                    <label class="position-absolute bottom-0 end-0 bg-white rounded-circle p-1 shadow-sm" style="cursor: pointer;">
                        <i class="fas fa-camera text-primary"></i>
                        <input type="file" wire:model="newAvatar" class="d-none" accept="image/*">
                    </label>
                </div>
                <h5 class="fw-bold mb-1">{{ Auth::user()->name }}</h5>
                <p class="text-muted small">{{ Auth::user()->email }}</p>
                <hr>
                <div class="text-start small">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Membre depuis</span>
                        <span>{{ Auth::user()->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Dernière connexion</span>
                        <span>{{ Auth::user()->last_login_at?->diffForHumans() ?? 'Première connexion' }}</span>
                    </div>
                </div>
                @error('newAvatar') <small class="text-danger d-block mt-2">{{ $message }}</small> @enderror
            </div>
        </div>

        <div class="col-md-8">
            <div class="card-modern card p-4">
                <h5 class="fw-bold mb-4">Informations personnelles</h5>
                <form wire:submit.prevent="updateProfile">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nom complet</label>
                        <input type="text" wire:model="name" class="form-control">
                        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" wire:model="email" class="form-control">
                        @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <button type="submit" class="btn btn-primary-custom">Mettre à jour</button>
                </form>

                <hr class="my-4">

                <h5 class="fw-bold mb-4">Changer le mot de passe</h5>
                <form wire:submit.prevent="updatePassword">
                    <div class="mb-3">
                        <label class="form-label">Mot de passe actuel</label>
                        <input type="password" wire:model="currentPassword" class="form-control">
                        @error('currentPassword') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nouveau mot de passe</label>
                        <input type="password" wire:model="newPassword" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirmer</label>
                        <input type="password" wire:model="newPasswordConfirmation" class="form-control">
                    </div>
                    @error('newPassword') <small class="text-danger d-block mb-3">{{ $message }}</small> @enderror
                    <button type="submit" class="btn btn-primary-custom">Changer le mot de passe</button>
                </form>
            </div>
        </div>
    </div>
</div>
