<div>
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            @if($collecte->status == 'fermee')
                <div class="alert alert-warning rounded-3">
                    <i class="fas fa-lock me-2"></i> Cette collecte est fermée.
                </div>
            @else
                <form wire:submit.prevent="submit" enctype="multipart/form-data">
                    @foreach($collecte->config_schema as $field)
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                {{ $field['label'] }}
                                @if($field['required'])<span class="text-danger">*</span>@endif
                            </label>

                            @if($field['type'] == 'text')
                                <input type="text" wire:model="formData.{{ $field['name'] }}" class="form-control rounded-3">

                            @elseif($field['type'] == 'textarea')
                                <textarea wire:model="formData.{{ $field['name'] }}" rows="3" class="form-control rounded-3"></textarea>

                            @elseif($field['type'] == 'number')
                                <input type="number" wire:model="formData.{{ $field['name'] }}" class="form-control rounded-3" step="any">

                            @elseif($field['type'] == 'email')
                                <input type="email" wire:model="formData.{{ $field['name'] }}" class="form-control rounded-3">

                            @elseif($field['type'] == 'date')
                                <input type="date" wire:model="formData.{{ $field['name'] }}" class="form-control rounded-3">

                            @elseif($field['type'] == 'select')
                                <select wire:model="formData.{{ $field['name'] }}" class="form-select rounded-3">
                                    <option value="">-- Sélectionnez --</option>
                                    @foreach($field['options'] ?? [] as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>

                            @elseif($field['type'] == 'radio')
                                @foreach($field['options'] ?? [] as $value => $label)
                                    <div class="form-check">
                                        <input type="radio" wire:model="formData.{{ $field['name'] }}" value="{{ $value }}" class="form-check-input">
                                        <label class="form-check-label">{{ $label }}</label>
                                    </div>
                                @endforeach

                            @elseif($field['type'] == 'file_image')
                                <div class="border rounded-3 p-3 bg-light"
                                     x-data="imageCompressor({
                                         fieldName: '{{ $field['name'] }}',
                                         maxSize:   512,
                                         quality:   0.92,
                                         maxSizeMB: {{ $field['max_size'] ?? 5 }}
                                     })">

                                    <label class="fw-semibold mb-2 d-block">
                                        <i class="fas fa-image me-2 text-primary"></i>Choisir une image
                                    </label>

                                    <input type="file"
                                           class="form-control rounded-3"
                                           accept="image/*"
                                           x-ref="fileInput"
                                           x-on:change="compress($event)">

                                    <div x-show="state === 'done'" class="mt-2">
                                        <span class="badge bg-success">
                                            ✓ <span x-text="fileName"></span>
                                        </span>
                                        <small class="text-muted ms-2">
                                            <span x-text="originalSize"></span> → <span x-text="compressedSize"></span>
                                            (<span x-text="ratio"></span>% allégé)
                                        </small>
                                    </div>

                                    <div x-show="state === 'error'" class="mt-2">
                                        <small class="text-danger" x-text="errorMsg"></small>
                                    </div>

                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Max {{ $field['max_size'] ?? 5 }} MB — compression (512x512, qualité 92%)
                                        </small>
                                    </div>

                                    <input type="file"
                                           wire:model="files.{{ $field['name'] }}"
                                           x-ref="livewireInput"
                                           style="display:none">

                                    <div wire:loading wire:target="files.{{ $field['name'] }}" class="mt-2">
                                        <span class="spinner-border spinner-border-sm text-primary me-1"></span>
                                        <small class="text-muted">Transfert vers le serveur…</small>
                                    </div>

                                    @error('files.' . $field['name'])
                                        <small class="text-danger d-block mt-2">{{ $message }}</small>
                                    @enderror
                                </div>

                            @elseif($field['type'] == 'file_audio')
                                <div class="border rounded-3 p-3 bg-light" x-data="{ fileName: '' }">
                                    <label class="fw-semibold mb-2 d-block">
                                        <i class="fas fa-headphones me-2 text-primary"></i>Choisir un fichier audio
                                    </label>
                                    <input type="file"
                                           wire:model="files.{{ $field['name'] }}"
                                           class="form-control rounded-3"
                                           accept="audio/*"
                                           x-on:change="fileName = $event.target.files[0]?.name || ''">
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Max {{ $field['max_size'] ?? 10 }} MB.
                                        </small>
                                    </div>
                                    <div x-show="fileName" class="mt-2">
                                        <span class="badge bg-success" x-text="'✓ ' + fileName"></span>
                                    </div>
                                    <div wire:loading wire:target="files.{{ $field['name'] }}" class="mt-2">
                                        <span class="spinner-border spinner-border-sm text-primary me-1"></span>
                                        <small class="text-muted">Transfert…</small>
                                    </div>
                                    @error('files.' . $field['name'])
                                        <small class="text-danger d-block mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            @endif

                            <div class="text-muted mt-1" style="font-size: 10px;">{{ $field['name'] }}</div>
                        </div>
                    @endforeach

                    <div wire:loading wire:target="submit" class="alert alert-info rounded-3 mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Chargement…</span>
                            </div>
                            <div>
                                <strong>Traitement en cours…</strong><br>
                                <small>Sauvegarde des données, veuillez patienter.</small>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary rounded-3 px-4 py-2" wire:loading.attr="disabled">
                        <span wire:loading.remove><i class="fas fa-paper-plane me-2"></i>Envoyer</span>
                        <span wire:loading><i class="fas fa-spinner fa-spin me-2"></i>Traitement…</span>
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
