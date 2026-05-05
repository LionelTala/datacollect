<div>
    <div class="mb-4">
        <h1 class="fw-bold">⚙️ Paramètres généraux</h1>
        <p class="text-muted">Configuration de la plateforme</p>
    </div>

    @if(session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Carte Maintenance -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-tools me-2 text-warning"></i>
                        Mode maintenance
                    </h5>
                    <p class="text-muted small mb-3">
                        Activer le mode maintenance pour bloquer l'accès aux utilisateurs.
                        Les administrateurs peuvent accéder via /datacollect
                    </p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Statut :
                            @if($maintenance_mode)
                                <span class="badge bg-danger">🔒 En maintenance</span>
                            @else
                                <span class="badge bg-success">✅ Actif</span>
                            @endif
                        </span>
                        <button wire:click="toggleMaintenance" class="btn {{ $maintenance_mode ? 'btn-success' : 'btn-warning' }} rounded-3">
                            {{ $maintenance_mode ? 'Réactiver le site' : 'Mettre en maintenance' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carte Cache -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-database me-2 text-info"></i>
                        Cache
                    </h5>
                    <p class="text-muted small mb-3">
                        Vider le cache de l'application (config, routes, vues, cache)
                    </p>
                    <button wire:click="clearCache" class="btn btn-outline-secondary rounded-3">
                        <i class="fas fa-trash-alt me-2"></i>Vider le cache
                    </button>
                </div>
            </div>
        </div>

        <!-- Carte Statistiques -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-chart-pie me-2 text-primary"></i>
                        Statistiques
                    </h5>
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="fw-bold fs-3">{{ \App\Models\User::count() }}</div>
                            <small class="text-muted">Utilisateurs</small>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold fs-3">{{ \App\Models\Collecte::count() }}</div>
                            <small class="text-muted">Collectes</small>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold fs-3">{{ \App\Models\DonneeCollecte::count() }}</div>
                            <small class="text-muted">Données</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carte Informations système -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-server me-2 text-success"></i>
                        Informations système
                    </h5>
                    <ul class="list-unstyled small">
                        <li><strong>Laravel :</strong> {{ app()->version() }}</li>
                        <li><strong>PHP :</strong> {{ phpversion() }}</li>
                        <li><strong>Environnement :</strong> {{ app()->environment() }}</li>
                        <li><strong>Dernière mise à jour :</strong> {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Carte Actions rapides -->
    <div class="card border-0 shadow-sm rounded-4 mt-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">
                <i class="fas fa-bolt me-2 text-warning"></i>
                Actions rapides
            </h5>
            <div class="d-flex gap-3 flex-wrap">
                <a href="{{ route('admin.users') }}" class="btn btn-outline-primary rounded-3">
                    <i class="fas fa-users me-2"></i>Gérer les utilisateurs
                </a>
                <a href="{{ route('admin.collectes') }}" class="btn btn-outline-success rounded-3">
                    <i class="fas fa-folder-open me-2"></i>Gérer les collectes
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary rounded-3">
                    <i class="fas fa-chart-line me-2"></i>Voir le dashboard
                </a>
            </div>
        </div>
    </div>
</div>
