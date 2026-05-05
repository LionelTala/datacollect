<div>
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">
                    <i class="fas fa-users me-2 text-primary"></i>
                    Équipe de la collecte
                </h5>
                @if($collecte->created_by == Auth::id())
                    <button class="btn btn-primary btn-sm rounded-3" data-bs-toggle="modal" data-bs-target="#inviteModal">
                        <i class="fas fa-plus me-1"></i> Inviter
                    </button>
                @endif
            </div>

            <!-- Messages flash -->
            @if(session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 mb-3">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-3">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row g-3">
                <!-- Créateur -->
                <div class="col-md-6 col-lg-4">
                    <div class="border rounded-3 p-3 bg-light">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-size: 16px;">
                                {{ substr($collecte->createur->name, 0, 2) }}
                            </div>
                            <div>
                                <div class="fw-bold">{{ $collecte->createur->name }}</div>
                                <div class="text-muted small">{{ $collecte->createur->email }}</div>
                                <span class="badge bg-primary mt-1">👑 Créateur</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Autres membres -->
                @foreach($members as $member)
                    @if($member->id != $collecte->created_by)
                        <div class="col-md-6 col-lg-4">
                            <div class="border rounded-3 p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle bg-secondary bg-opacity-25 text-dark d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-size: 16px;">
                                            {{ substr($member->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $member->name }}</div>
                                            <div class="text-muted small">{{ $member->email }}</div>
                                            <select wire:change="changeRole({{ $member->id }}, $event.target.value)"
                                                    class="form-select form-select-sm mt-1" style="width: auto;">
                                                <option value="collecteur" {{ $member->pivot->role == 'collecteur' ? 'selected' : '' }}>📝 Collecteur</option>
                                                <option value="superviseur" {{ $member->pivot->role == 'superviseur' ? 'selected' : '' }}>👑 Superviseur</option>
                                                <option value="analyste" {{ $member->pivot->role == 'analyste' ? 'selected' : '' }}>📊 Analyste</option>
                                            </select>
                                        </div>
                                    </div>
                                    @if($collecte->created_by == Auth::id())
                                        <button wire:click="removeMember({{ $member->id }})"
                                                class="btn btn-sm btn-outline-danger rounded-3"
                                                onclick="return confirm('Retirer ce membre ?')">
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
                    <h5 class="fw-bold">
                        <i class="fas fa-user-plus me-2 text-primary"></i>
                        Inviter un utilisateur
                    </h5>
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
                            <option value="collecteur">📝 Collecteur - Peut soumettre des données</option>
                            <option value="superviseur">👑 Superviseur - Peut voir et exporter</option>
                            <option value="analyste">📊 Analyste - Peut analyser les données</option>
                        </select>
                    </div>
                    <button wire:click="sendInvitation" class="btn btn-primary w-100 rounded-3 py-2" data-bs-dismiss="modal">
                        <i class="fas fa-paper-plane me-2"></i>Envoyer l'invitation
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
