<div>
    @if (session()->has('profile-message'))
        <div class="alert alert-success alert-dismissible fade show rounded-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('profile-message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Avatar -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 text-center p-4">
                <div class="mb-3">
                    @if($avatar)
                        <img src="{{ Storage::url($avatar) }}" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 150px; height: 150px;">
                            <i class="fas fa-user fa-4x text-muted"></i>
                        </div>
                    @endif
                </div>

                <label class="btn btn-outline-primary">
                    <i class="fas fa-camera me-2"></i>Changer l'avatar
                    <input type="file" wire:model="newAvatar" class="d-none" accept="image/*">
                </label>
                @error('newAvatar') <small class="text-danger d-block mt-2">{{ $message }}</small> @enderror

                @if($newAvatar)
                    <div class="mt-2">
                        <small class="text-muted">Nouvelle image chargée</small>
                        <div class="spinner-border spinner-border-sm text-primary ms-2" wire:loading wire:target="newAvatar"></div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Informations -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-4">
                        <i class="fas fa-user-edit me-2" style="color: #3B82F6;"></i>
                        Informations personnelles
                    </h4>

                    <form wire:submit.prevent="updateProfile">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nom complet</label>
                            <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" wire:model="email" class="form-control @error('email') is-invalid @enderror">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <hr class="my-4">

                        <h5 class="fw-bold mb-3">Changer le mot de passe</h5>

                        <div class="mb-3">
                            <label class="form-label">Mot de passe actuel</label>
                            <input type="password" wire:model="currentPassword" class="form-control @error('currentPassword') is-invalid @enderror">
                            @error('currentPassword') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nouveau mot de passe</label>
                            <input type="password" wire:model="newPassword" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Confirmer le mot de passe</label>
                            <input type="password" wire:model="newPasswordConfirmation" class="form-control">
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
