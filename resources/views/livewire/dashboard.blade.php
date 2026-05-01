<div>
    <div class="mb-4">
        <h1 class="fw-bold mb-1">Dashboard</h1>
        <p class="text-muted">Bienvenue {{ Auth::user()->name }} 👋</p>
    </div>

    <!-- Statistiques -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card-modern card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small">Total collectes</span>
                        <h2 class="fw-bold mb-0">{{ $this->stats['total'] }}</h2>
                    </div>
                    <i class="fas fa-folder-open fa-2x text-primary opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-modern card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small">Actives</span>
                        <h2 class="fw-bold mb-0">{{ $this->stats['active'] }}</h2>
                    </div>
                    <i class="fas fa-play-circle fa-2x text-success opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-modern card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small">Brouillons</span>
                        <h2 class="fw-bold mb-0">{{ $this->stats['brouillon'] }}</h2>
                    </div>
                    <i class="fas fa-pencil-alt fa-2x text-warning opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-modern card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small">Fermées</span>
                        <h2 class="fw-bold mb-0">{{ $this->stats['fermee'] }}</h2>
                    </div>
                    <i class="fas fa-lock fa-2x text-secondary opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Dernières collectes -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Dernières collectes</h4>
        <a href="{{ route('collectes.list') }}" class="btn btn-sm btn-outline-custom">Voir tout</a>
    </div>

    @if($this->recentCollectes->count() > 0)
        <div class="row g-3">
            @foreach($this->recentCollectes as $collecte)
                <div class="col-md-6">
                    <div class="card-modern card p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="fw-bold mb-1">{{ $collecte->nom }}</h6>
                                <p class="text-muted small mb-0">{{ Str::limit($collecte->description, 60) ?: 'Aucune description' }}</p>
                            </div>
                            <span class="badge-modern {{ $collecte->status == 'active' ? 'bg-success bg-opacity-10 text-success' : 'bg-secondary bg-opacity-10 text-secondary' }}">
                                {{ ucfirst($collecte->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card-modern card text-center py-5">
            <i class="fas fa-folder-open fa-3x text-muted mb-3 opacity-25"></i>
            <p class="text-muted">Aucune collecte pour le moment</p>
            <a href="{{ route('collecte.create') }}" class="btn btn-primary-custom">Créer ma première collecte</a>
        </div>
    @endif
</div>
