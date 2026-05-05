<div>
    <div class="mb-4">
        <h1 class="fw-bold">👑 Administration</h1>
        <p class="text-muted">Gestion globale de la plateforme</p>
    </div>

    <!-- Stats -->
    <div class="row g-4 mb-5">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small">Utilisateurs</span>
                        <h2 class="fw-bold mb-0">{{ $stats['total_users'] }}</h2>
                    </div>
                    <i class="fas fa-users fa-2x text-primary opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small">Collectes</span>
                        <h2 class="fw-bold mb-0">{{ $stats['total_collectes'] }}</h2>
                    </div>
                    <i class="fas fa-folder-open fa-2x text-success opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small">Données collectées</span>
                        <h2 class="fw-bold mb-0">{{ $stats['total_donnees'] }}</h2>
                    </div>
                    <i class="fas fa-database fa-2x text-info opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small">Collectes actives</span>
                        <h2 class="fw-bold mb-0">{{ $stats['active_collectes'] }}</h2>
                    </div>
                    <i class="fas fa-play fa-2x text-warning opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Activité récente -->
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-user-plus me-2 text-primary"></i>
                        Nouveaux utilisateurs (aujourd'hui)
                    </h5>
                    <h2 class="fw-bold">{{ $stats['new_users_today'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-chart-line me-2 text-success"></i>
                        Nouvelles données (aujourd'hui)
                    </h5>
                    <h2 class="fw-bold">{{ $stats['new_donnees_today'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Liens rapides -->
    <div class="row g-3 mt-4">
        <div class="col-md-4">
            <a href="{{ route('admin.users') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 p-3 text-center hover-card">
                    <i class="fas fa-users fa-2x text-primary mb-2"></i>
                    <h6 class="fw-bold mb-0">Gérer les utilisateurs</h6>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.collectes') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 p-3 text-center hover-card">
                    <i class="fas fa-folder-open fa-2x text-success mb-2"></i>
                    <h6 class="fw-bold mb-0">Gérer les collectes</h6>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.settings') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 p-3 text-center hover-card">
                    <i class="fas fa-cog fa-2x text-secondary mb-2"></i>
                    <h6 class="fw-bold mb-0">Paramètres</h6>
                </div>
            </a>
        </div>
    </div>
</div>
