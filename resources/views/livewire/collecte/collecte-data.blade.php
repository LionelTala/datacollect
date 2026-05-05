<div>
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <!-- Barre de recherche -->
            <div class="p-3 border-bottom bg-light">
                <div class="row g-2 align-items-center">
                    <div class="col-md-6">
                        <input type="text"
                               wire:model.live="search"
                               class="form-control rounded-3"
                               placeholder="🔍 Rechercher par utilisateur ou ID...">
                    </div>
                    <div class="col-md-2">
                        <select wire:model.live="perPage" class="form-select rounded-3">
                            <option value="10">10 par page</option>
                            <option value="20">20 par page</option>
                            <option value="50">50 par page</option>
                            <option value="100">100 par page</option>
                        </select>
                    </div>
                    <div class="col-md-4 text-end">
                        <span class="text-muted small">Total: {{ $donnees->total() }} données</span>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">Utilisateur</th>
                            <th class="py-3">Date</th>
                            @foreach($collecte->config_schema as $field)
                                <th class="py-3">{{ $field['label'] }}</th>
                            @endforeach
                            <th class="pe-4 py-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($donnees as $donnee)
                            @php
                                $canDelete = ($collecte->created_by == Auth::id()) || ($donnee->user_id == Auth::id());
                            @endphp
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 12px;">
                                            {{ substr($donnee->utilisateur->name, 0, 2) }}
                                        </div>
                                        {{ $donnee->utilisateur->name }}
                                    </div>
                                 </td>
                                <td class="py-3">{{ $donnee->created_at->format('d/m/Y H:i') }}</td>
                                @foreach($collecte->config_schema as $field)
                                    <td class="py-3">
                                        @php $value = $donnee->data[$field['name']] ?? null; @endphp
                                        @if(str_starts_with($field['type'], 'file_') && $value)
                                            <a href="{{ Storage::url($value) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-3">
                                                <i class="fas {{ $field['type'] == 'file_image' ? 'fa-image' : 'fa-file-audio' }} me-1"></i>
                                                {{ $field['type'] == 'file_image' ? 'Voir' : 'Écouter' }}
                                            </a>
                                        @else
                                            {{ is_array($value) ? json_encode($value) : $value }}
                                        @endif
                                    </td>
                                @endforeach
                                <td class="pe-4 py-3 text-end">
                                    @if($canDelete)
                                        <button wire:click="deleteDonnee({{ $donnee->id }})"
                                                class="btn btn-sm btn-outline-danger rounded-3"
                                                onclick="return confirm('Supprimer cette donnée ?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 3 + count($collecte->config_schema) }}" class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-2 opacity-25"></i>
                                    <p class="text-muted mb-0">Aucune donnée collectée</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-3 border-top bg-light">
                {{ $donnees->links() }}
            </div>
        </div>
    </div>

    <!-- Top contributeurs -->
    @if($topContributors->count() > 0)
        <div class="card border-0 shadow-sm rounded-4 mt-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-trophy me-2 text-warning"></i>
                    Top contributeurs
                </h5>
                <div class="d-flex gap-3 flex-wrap">
                    @foreach($topContributors as $contributor)
                        <div class="text-center p-3 bg-light rounded-3">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 50px; height: 50px; font-size: 18px;">
                                {{ substr($contributor->utilisateur->name, 0, 2) }}
                            </div>
                            <div class="fw-semibold">{{ $contributor->utilisateur->name }}</div>
                            <small class="text-muted">{{ $contributor->total }} données</small>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
