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
        @livewire('collecte.collecte-form', ['collecteId' => $collecte->id], key('form-'.$collecte->id))
    @endif

    <!-- TAB DONNEES -->
    @if($activeTab == 'data')
        @livewire('collecte.collecte-data', ['collecteId' => $collecte->id], key('data-'.$collecte->id))
    @endif

    <!-- TAB ANALYSE -->
    @if($activeTab == 'analyse')
        @livewire('analyse.analyse-collecte', ['id' => $collecte->id], key('analyse-'.$collecte->id))
    @endif

    <!-- TAB EQUIPE -->
    @if($activeTab == 'members')
        @livewire('collecte.collecte-members', ['collecteId' => $collecte->id], key('members-'.$collecte->id))
    @endif

    <!-- TAB CONFIGURATION -->
    @if($activeTab == 'settings')
        @livewire('collecte.collecte-settings', ['collecteId' => $collecte->id], key('settings-'.$collecte->id))
    @endif
</div>
