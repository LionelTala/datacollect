<div>
    <!-- En-tête -->
    <div class="mb-4">
        <div class="d-flex align-items-center gap-3 mb-2">
            <div class="rounded-3 p-2" style="background: linear-gradient(135deg, #10b981, #059669); width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-chart-line text-white"></i>
            </div>
            <div>
                <h1 class="fw-bold mb-0" style="font-size: 1.75rem;">Analyse - {{ $collecte->nom }}</h1>
                <p class="text-muted mb-0">Visualisation et analyse des données collectées</p>
            </div>
        </div>
        <a href="{{ route('collecte.show', $collecte->id) }}" class="text-muted text-decoration-none">
            <i class="fas fa-arrow-left me-1"></i> Retour à la collecte
        </a>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4 text-center p-3">
                <div class="fw-bold fs-4">{{ $stats['total'] }}</div>
                <small class="text-muted">Total données</small>
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
                <small class="text-muted">7 derniers jours</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4 text-center p-3">
                <div class="fw-bold fs-4">{{ $stats['last_month'] }}</div>
                <small class="text-muted">30 derniers jours</small>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-uppercase text-muted">Période</label>
                    <select wire:model="dateRange" class="form-select rounded-3">
                        <option value="all">Toutes les dates</option>
                        <option value="week">7 derniers jours</option>
                        <option value="month">30 derniers jours</option>
                        <option value="custom">Personnalisée</option>
                    </select>
                </div>
                @if($dateRange == 'custom')
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small text-uppercase text-muted">Du</label>
                        <input type="date" wire:model="startDate" class="form-control rounded-3">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small text-uppercase text-muted">Au</label>
                        <input type="date" wire:model="endDate" class="form-control rounded-3">
                    </div>
                @endif
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-uppercase text-muted">Champ à analyser</label>
                    <select wire:model="selectedField" class="form-select rounded-3">
                        @foreach($collecte->config_schema as $field)
                            @if(in_array($field['type'], ['select', 'radio', 'text', 'number']))
                                <option value="{{ $field['name'] }}">{{ $field['label'] }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button wire:click="exportCSV" class="btn btn-outline-success rounded-3 flex-grow-1">
                        <i class="fas fa-file-csv me-1"></i> CSV
                    </button>
                    <button class="btn btn-outline-info rounded-3 flex-grow-1">
                        <i class="fas fa-file-excel me-1"></i> Excel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique -->
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-chart-bar me-2 text-primary"></i>
                            {{ $chartData['title'] ?? 'Distribution' }}
                        </h5>
                        <div class="btn-group btn-group-sm">
                            <button wire:click="$set('activeChart', 'bar')" class="btn {{ $activeChart == 'bar' ? 'btn-primary' : 'btn-outline-secondary' }}">
                                <i class="fas fa-chart-bar"></i>
                            </button>
                            <button wire:click="$set('activeChart', 'pie')" class="btn {{ $activeChart == 'pie' ? 'btn-primary' : 'btn-outline-secondary' }}">
                                <i class="fas fa-chart-pie"></i>
                            </button>
                            <button wire:click="$set('activeChart', 'line')" class="btn {{ $activeChart == 'line' ? 'btn-primary' : 'btn-outline-secondary' }}">
                                <i class="fas fa-chart-line"></i>
                            </button>
                        </div>
                    </div>

                    @if(count($chartData['labels'] ?? []) > 0)
                        <canvas id="statsChart" width="400" height="300" style="max-height: 300px;"></canvas>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-chart-simple fa-3x text-muted mb-3 opacity-25"></i>
                            <p class="text-muted">Aucune donnée à afficher</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-table me-2 text-primary"></i>
                        Détail des valeurs
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="bg-light">
                                <tr>
                                    <th>Valeur</th>
                                    <th class="text-end">Nombre</th>
                                    <th class="text-end">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($chartData['labels'] ?? [] as $index => $label)
                                    @php
                                        $total = array_sum($chartData['values']);
                                        $percent = $total > 0 ? round(($chartData['values'][$index] / $total) * 100, 1) : 0;
                                    @endphp
                                    <tr>
                                        <td class="small">{{ $label }}</td>
                                        <td class="text-end">{{ $chartData['values'][$index] }}</td>
                                        <td class="text-end text-muted">{{ $percent }}%</td>
                                    </tr>
                                @endforeach
                                @if(count($chartData['labels'] ?? []) == 0)
                                    <tr>
                                        <td colspan="3" class="text-center py-3 text-muted">Aucune donnée</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('livewire:load', function () {
        Livewire.on('updateChart', (chartData) => {
            if (window.myChart) window.myChart.destroy();

            const ctx = document.getElementById('statsChart').getContext('2d');
            const type = '{{ $activeChart }}';

            window.myChart = new Chart(ctx, {
                type: type,
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Distribution',
                        data: chartData.values,
                        backgroundColor: [
                            '#4361ee', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6',
                            '#ec4899', '#14b8a6', '#f97316', '#6366f1', '#06b6d4'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        });

        // Premier chargement
        @if(count($chartData['labels'] ?? []) > 0)
            Livewire.emit('updateChart', {{ json_encode($chartData) }});
        @endif

        // Écouter les changements
        Livewire.on('chartDataUpdated', (data) => {
            if (window.myChart) {
                window.myChart.data.labels = data.labels;
                window.myChart.data.datasets[0].data = data.values;
                window.myChart.update();
            }
        });
    });
</script>
@endpush
