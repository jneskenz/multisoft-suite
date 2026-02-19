<div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-12 col-md-4">
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="ti tabler-search"></i></span>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        class="form-control"
                        placeholder="{{ __('Buscar por nombre, código...') }}"
                    >
                    <span class="input-group-text cursor-pointer">
                        <div wire:loading wire:target="search" class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Buscando...</span>
                        </div>
                        @if ($search)
                            <i class="ti tabler-x" wire:click="$set('search', '')"></i>
                        @endif
                    </span>
                </div>
            </div>

            <div class="col-6 col-md-2">
                <select wire:model.live="tipoFilter" class="form-select">
                    <option value="">{{ __('Todos los tipos') }}</option>
                    @foreach ($this->tiposDocumento as $tipo)
                        <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-2">
                <select wire:model.live="statusFilter" class="form-select">
                    <option value="">{{ __('Todos') }}</option>
                    <option value="activo">{{ __('Activos') }}</option>
                    <option value="inactivo">{{ __('Inactivos') }}</option>
                    <option value="borrador">{{ __('Borrador') }}</option>
                </select>
            </div>

            <div class="col-6 col-md-2">
                <select wire:model.live="perPage" class="form-select">
                    <option value="10">10 por página</option>
                    <option value="25">25 por página</option>
                    <option value="50">50 por página</option>
                </select>
            </div>

            <div class="col-6 col-md-2 d-flex gap-1">
                @if ($search || $tipoFilter || $statusFilter)
                    <button wire:click="clearFilters" class="btn btn-outline-secondary w-100" title="{{ __('Limpiar filtros') }}">
                        <i class="ti tabler-filter-off"></i>
                    </button>
                @endif
                <button
                    wire:click="toggleTrashed"
                    class="btn {{ $showTrashed ? 'btn-danger' : 'btn-outline-secondary' }}"
                    title="{{ $showTrashed ? __('Ver activos') : __('Ver eliminados') }}"
                >
                    <i class="ti {{ $showTrashed ? 'tabler-file-text' : 'tabler-trash' }}"></i>
                </button>
            </div>

            @if ($showTrashed)
                <div class="col-12">
                    <div class="alert alert-warning d-flex align-items-center mb-0" role="alert">
                        <i class="ti tabler-trash me-2"></i>
                        <span>{{ __('Mostrando plantillas eliminadas. Puedes restaurarlas o eliminarlas permanentemente.') }}</span>
                    </div>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 130px;">{{ __('Acciones') }}</th>
                            <th style="width: 100px;">{{ __('Estado') }}</th>
                            <th wire:click="sortBy('nombre')" class="cursor-pointer" style="min-width: 220px;">
                                <div class="d-flex align-items-center">
                                    {{ __('Plantilla') }}
                                    @if ($sortField === 'nombre')
                                        <i class="ti tabler-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </div>
                            </th>
                            <th wire:click="sortBy('codigo')" class="cursor-pointer" style="min-width: 150px;">
                                <div class="d-flex align-items-center">
                                    {{ __('Código') }}
                                    @if ($sortField === 'codigo')
                                        <i class="ti tabler-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </div>
                            </th>
                            <th style="min-width: 160px;">{{ __('Tipo') }}</th>
                            <th wire:click="sortBy('version')" class="cursor-pointer" style="width: 100px;">
                                <div class="d-flex align-items-center">
                                    {{ __('Versión') }}
                                    @if ($sortField === 'version')
                                        <i class="ti tabler-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </div>
                            </th>
                            <th style="width: 110px;">{{ __('Predet.') }}</th>
                            <th wire:click="sortBy('created_at')" class="cursor-pointer" style="width: 140px;">
                                <div class="d-flex align-items-center">
                                    {{ __('Creado') }}
                                    @if ($sortField === 'created_at')
                                        <i class="ti tabler-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </div>
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($this->plantillas as $plantilla)
                            <tr wire:key="plantilla-{{ $plantilla->id }}">
                                <td>
                                    <div class="d-flex justify-content-start align-items-center gap-1">
                                        <button
                                            class="btn btn-sm rounded-pill btn-icon btn-label-secondary waves-effect me-1"
                                            data-bs-toggle="tooltip"
                                            title="Nro: {{ $plantilla->id }}"
                                        >
                                            {{ $plantilla->id }}
                                        </button>

                                        <button
                                            wire:click="edit({{ $plantilla->id }})"
                                            class="btn btn-sm btn-icon btn-label-primary"
                                            data-bs-toggle="tooltip"
                                            title="{{ __('Editar') }}"
                                        >
                                            <i class="ti tabler-edit"></i>
                                        </button>

                                        <div class="dropdown">
                                            <button
                                                class="btn btn-sm btn-icon border btn-text-secondary rounded dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown"
                                            >
                                                <i class="ti tabler-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                @if (!$showTrashed)
                                                    <li>
                                                        <a class="dropdown-item d-flex p-2 justify-content-start btn btn-label-info align-items-center"
                                                            wire:click="showHistorial({{ $plantilla->id }})">
                                                            <button class="btn btn-sm btn-icon btn-label-info me-2"><i class="ti tabler-history"></i></button>
                                                            <span class="lh-1">{{ __('Historial') }}</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item d-flex p-2 justify-content-start btn btn-label-success align-items-center"
                                                            href="{{ url(app()->getLocale() . '/' . (request()->route('group') ?? session('current_group_code')) . '/hr/plantillas/preview-pdf?id=' . $plantilla->id) }}"
                                                            target="_blank">
                                                            <button class="btn btn-sm btn-icon btn-label-success me-2"><i class="ti tabler-file-type-pdf"></i></button>
                                                            <span class="lh-1">{{ __('Preview PDF') }}</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item d-flex p-2 justify-content-start btn btn-label-warning align-items-center"
                                                            wire:click="toggleEstado({{ $plantilla->id }})">
                                                            <button class="btn btn-sm btn-icon btn-label-warning me-2"><i class="ti tabler-refresh"></i></button>
                                                            <span class="lh-1">{{ __('Cambiar estado') }}</span>
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item d-flex p-2 justify-content-start btn btn-label-danger align-items-center"
                                                            wire:click="confirmDelete({{ $plantilla->id }})">
                                                            <button class="btn btn-sm btn-icon btn-label-danger me-2"><i class="ti tabler-trash"></i></button>
                                                            <span class="lh-1">{{ __('Eliminar') }}</span>
                                                        </a>
                                                    </li>
                                                @else
                                                    <li>
                                                        <a class="dropdown-item d-flex p-2 justify-content-start btn btn-label-success align-items-center"
                                                            wire:click="confirmRestore({{ $plantilla->id }})">
                                                            <button class="btn btn-sm btn-icon btn-label-success me-2"><i class="ti tabler-refresh"></i></button>
                                                            <span class="lh-1">{{ __('Restaurar') }}</span>
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item d-flex p-2 justify-content-start btn btn-label-dark align-items-center"
                                                            wire:click="confirmForceDelete({{ $plantilla->id }})">
                                                            <button class="btn btn-sm btn-icon btn-label-dark me-2"><i class="ti tabler-trash-x"></i></button>
                                                            <span class="lh-1">{{ __('Eliminar permanente') }}</span>
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $estadoClass = match($plantilla->estado) {
                                            '1' => 'bg-label-success',
                                            '0' => 'bg-label-secondary',
                                            default => 'bg-label-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $estadoClass }}">
                                        {{ $plantilla->estado_label }}
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <h6 class="mb-0">{{ $plantilla->nombre }}</h6>
                                        @if($plantilla->descripcion)
                                            <small class="text-muted">{{ \Illuminate\Support\Str::limit($plantilla->descripcion, 50) }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-label-info">{{ $plantilla->codigo }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-label-primary">
                                        {{ $plantilla->tipoDocumento?->nombre ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-label-dark">v{{ $plantilla->version }}</span>
                                </td>
                                <td>
                                    @if($plantilla->es_predeterminada)
                                        <span class="badge bg-label-success"><i class="ti tabler-star-filled me-1"></i>Sí</span>
                                    @else
                                        <span class="text-muted">No</span>
                                    @endif
                                </td>
                                <td>
                                    @if($plantilla->created_at)
                                        <span title="{{ $plantilla->created_at->format('d/m/Y H:i') }}">
                                            {{ $plantilla->created_at->diffForHumans() }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="ti tabler-file-off icon-48px text-muted mb-3"></i>
                                        <h6 class="mb-1">{{ __('No se encontraron plantillas') }}</h6>
                                        <p class="text-muted mb-0">
                                            @if ($search || $tipoFilter || $statusFilter)
                                                {{ __('Intenta ajustar los filtros de búsqueda') }}
                                            @else
                                                {{ __('Aún no hay plantillas registradas') }}
                                            @endif
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($this->plantillas->hasPages())
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        {{ __('Mostrando') }} {{ $this->plantillas->firstItem() }} - {{ $this->plantillas->lastItem() }}
                        {{ __('de') }} {{ $this->plantillas->total() }} {{ __('plantillas') }}
                    </div>
                    {{ $this->plantillas->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Crear/Editar --}}
    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="ti tabler-{{ $isEditing ? 'pencil' : 'plus' }} me-2"></i>
                            {{ $isEditing ? __('Editar Plantilla') : __('Nueva Plantilla') }}
                        </h5>
                        <button type="button" wire:click="closeModal" class="btn-close"></button>
                    </div>

                    <form wire:submit="save">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Tipo de Documento') }} <span class="text-danger">*</span></label>
                                    <select wire:model.live="tipo_documento_id" class="form-select @error('tipo_documento_id') is-invalid @enderror">
                                        <option value="">{{ __('Seleccionar...') }}</option>
                                        @foreach ($this->tiposDocumento as $tipo)
                                            <option value="{{ $tipo->id }}">{{ $tipo->codigo }} - {{ $tipo->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('tipo_documento_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Categoría') }}</label>
                                    <select wire:model="categoria_documento_id" class="form-select @error('categoria_documento_id') is-invalid @enderror">
                                        <option value="">{{ __('Sin categoría') }}</option>
                                        @foreach ($this->categoriasDocumento as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('categoria_documento_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Código') }} <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="codigo" class="form-control @error('codigo') is-invalid @enderror" placeholder="Ej: PLT-CONT-001">
                                    @error('codigo')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Nombre') }} <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="nombre" class="form-control @error('nombre') is-invalid @enderror" placeholder="Nombre de la plantilla">
                                    @error('nombre')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label">{{ __('Descripción') }}</label>
                                    <textarea wire:model="descripcion" class="form-control" rows="2" placeholder="Descripción opcional"></textarea>
                                </div>

                                <div class="col-12" wire:ignore>
                                    <label class="form-label">{{ __('Contenido HTML') }}</label>
                                    <div
                                        x-data="{
                                            quill: null,
                                            contenidoInicial: @js($contenido_html),
                                            init() {
                                                this.$nextTick(() => {
                                                    this.quill = new Quill(this.$refs.editor, {
                                                        theme: 'snow',
                                                        placeholder: 'Escribe el contenido de la plantilla aquí...',
                                                        modules: {
                                                            toolbar: [
                                                                [{ 'header': [1, 2, 3, 4, false] }],
                                                                ['bold', 'italic', 'underline', 'strike'],
                                                                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                                                                [{ 'align': [] }],
                                                                [{ 'indent': '-1' }, { 'indent': '+1' }],
                                                                ['blockquote'],
                                                                ['clean']
                                                            ]
                                                        }
                                                    });

                                                    // Load existing content
                                                    if (this.contenidoInicial) {
                                                        this.quill.root.innerHTML = this.contenidoInicial;
                                                    }

                                                    // Sync editor → Livewire on every change
                                                    this.quill.on('text-change', () => {
                                                        $wire.set('contenido_html', this.quill.root.innerHTML, false);
                                                    });

                                                    // Listen for content reload (when editing a different template)
                                                    Livewire.on('quill-load-content', (data) => {
                                                        if (data[0] && data[0].html) {
                                                            this.quill.root.innerHTML = data[0].html;
                                                        } else {
                                                            this.quill.root.innerHTML = '';
                                                        }
                                                    });
                                                });
                                            }
                                        }"
                                    >
                                        <div x-ref="editor" style="min-height: 200px; text-align: left;"></div>
                                    </div>
                                    <small class="text-muted">Usa variables como @{{empleado.nombres}}, @{{empresa.razon_social}}, @{{contrato.fecha_inicio}}, etc.</small>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">{{ __('Idioma') }}</label>
                                    <select wire:model="idioma" class="form-select">
                                        <option value="es">Español</option>
                                        <option value="en">Inglés</option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">{{ __('Formato') }}</label>
                                    <select wire:model="formato_papel" class="form-select">
                                        <option value="A4">A4</option>
                                        <option value="Letter">Letter</option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">{{ __('Orientación') }}</label>
                                    <select wire:model="orientacion" class="form-select">
                                        <option value="vertical">Vertical</option>
                                        <option value="horizontal">Horizontal</option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">{{ __('Versión') }}</label>
                                    <input type="number" wire:model="version" class="form-control" placeholder="1.0">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">{{ __('Estado') }}</label>
                                    <select wire:model="estado" class="form-select">
                                        <option value="1">Activo</option>
                                        <option value="0">Inactivo</option>
                                    </select>
                                </div>

                                <div class="col-md-4 d-flex align-items-end">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" wire:model="es_predeterminada" id="es_predeterminada">
                                        <label class="form-check-label" for="es_predeterminada">{{ __('Predeterminada') }}</label>
                                    </div>
                                </div>
                            </div>

                            {{-- Secciones Asignadas --}}
                            <div class="p-3 pt-0">
                                <h6 class="mb-3"><i class="ti tabler-puzzle me-1"></i>{{ __('Secciones Asignadas') }}</h6>

                                <div class="input-group mb-3">
                                    <select wire:model.live="seccionIdToAdd" class="form-select">
                                        <option value="">{{ __('Seleccionar sección...') }}</option>
                                        @foreach ($this->seccionesDisponibles as $sec)
                                            <option value="{{ $sec->id }}">[{{ $sec->codigo }}] {{ $sec->nombre }}</option>
                                        @endforeach
                                    </select>
                                    <button wire:click.prevent="addSeccion" class="btn btn-outline-primary" type="button">
                                        <i class="ti tabler-plus"></i> {{ __('Agregar') }}
                                    </button>
                                </div>

                                @if (count($seccionesAsignadas) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 50px">#</th>
                                                    <th>{{ __('Sección') }}</th>
                                                    <th style="width: 110px">{{ __('Ubicación') }}</th>
                                                    <th style="width: 80px" class="text-center">{{ __('Oblig.') }}</th>
                                                    <th style="width: 80px" class="text-center">{{ __('Edit.') }}</th>
                                                    <th style="width: 100px" class="text-center">{{ __('Acciones') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($seccionesAsignadas as $index => $sec)
                                                    <tr>
                                                        <td class="text-center">{{ $sec['orden'] }}</td>
                                                        <td>
                                                            <span class="fw-medium">{{ $sec['nombre'] }}</span>
                                                            <br><small class="text-muted">{{ $sec['codigo'] }} · {{ $sec['categoria'] ?? 'general' }}</small>
                                                        </td>
                                                        <td>
                                                            <select wire:model="seccionesAsignadas.{{ $index }}.ubicacion" class="form-select form-select-sm">
                                                                <option value="inicio">Inicio</option>
                                                                <option value="cuerpo">Cuerpo</option>
                                                                <option value="final">Final</option>
                                                            </select>
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="checkbox" class="form-check-input" wire:model="seccionesAsignadas.{{ $index }}.es_obligatoria">
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="checkbox" class="form-check-input" wire:model="seccionesAsignadas.{{ $index }}.es_editable">
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="btn-group btn-group-sm">
                                                                <button type="button" wire:click="moveSeccion({{ $index }}, 'up')" class="btn btn-outline-secondary btn-sm" {{ $index === 0 ? 'disabled' : '' }}>
                                                                    <i class="ti tabler-arrow-up ti-xs"></i>
                                                                </button>
                                                                <button type="button" wire:click="moveSeccion({{ $index }}, 'down')" class="btn btn-outline-secondary btn-sm" {{ $index === count($seccionesAsignadas) - 1 ? 'disabled' : '' }}>
                                                                    <i class="ti tabler-arrow-down ti-xs"></i>
                                                                </button>
                                                                <button type="button" wire:click="removeSeccion({{ $index }})" class="btn btn-outline-danger btn-sm">
                                                                    <i class="ti tabler-x ti-xs"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center text-muted py-2">
                                        <small><i class="ti tabler-puzzle-off me-1"></i>{{ __('No hay secciones asignadas. Agrega secciones reutilizables arriba.') }}</small>
                                    </div>
                                @endif
                            </div>

                        <div class="modal-footer">
                            <button type="button" wire:click="closeModal" class="btn btn-outline-secondary">
                                {{ __('Cancelar') }}
                            </button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="save">
                                    <i class="ti tabler-device-floppy me-1"></i>
                                    {{ $isEditing ? __('Actualizar') : __('Crear Plantilla') }}
                                </span>
                                <span wire:loading wire:target="save">
                                    <span class="spinner-border spinner-border-sm me-1"></span>
                                    {{ __('Guardando...') }}
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Eliminar --}}
    @if ($showDeleteModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center py-4">
                        <div class="mb-3"><i class="ti tabler-alert-triangle icon-48px text-warning"></i></div>
                        <h5>{{ __('¿Eliminar plantilla?') }}</h5>
                        <p class="text-muted mb-4">{{ __('La plantilla se moverá a la papelera.') }}</p>
                        <div class="d-flex justify-content-center gap-2">
                            <button wire:click="closeModal" class="btn btn-outline-secondary">{{ __('Cancelar') }}</button>
                            <button wire:click="delete" class="btn btn-danger" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="delete"><i class="ti tabler-trash me-1"></i>{{ __('Eliminar') }}</span>
                                <span wire:loading wire:target="delete"><span class="spinner-border spinner-border-sm"></span></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Restaurar --}}
    @if ($showRestoreModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center py-4">
                        <div class="mb-3"><i class="ti tabler-refresh icon-48px text-success"></i></div>
                        <h5>{{ __('¿Restaurar plantilla?') }}</h5>
                        <p class="text-muted mb-4">{{ __('La plantilla volverá a estar disponible.') }}</p>
                        <div class="d-flex justify-content-center gap-2">
                            <button wire:click="closeModal" class="btn btn-outline-secondary">{{ __('Cancelar') }}</button>
                            <button wire:click="restore" class="btn btn-success" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="restore"><i class="ti tabler-refresh me-1"></i>{{ __('Restaurar') }}</span>
                                <span wire:loading wire:target="restore"><span class="spinner-border spinner-border-sm"></span></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Eliminar Permanente --}}
    @if ($showForceDeleteModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center py-4">
                        <div class="mb-3"><i class="ti tabler-trash-x icon-48px text-danger"></i></div>
                        <h5>{{ __('¿Eliminar permanentemente?') }}</h5>
                        <p class="text-muted mb-4">{{ __('Esta acción no se puede deshacer.') }}</p>
                        <div class="d-flex justify-content-center gap-2">
                            <button wire:click="closeModal" class="btn btn-outline-secondary">{{ __('Cancelar') }}</button>
                            <button wire:click="forceDelete" class="btn btn-danger" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="forceDelete"><i class="ti tabler-trash-x me-1"></i>{{ __('Eliminar') }}</span>
                                <span wire:loading wire:target="forceDelete"><span class="spinner-border spinner-border-sm"></span></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    {{-- Modal Historial de Versiones --}}
    @if ($showHistorialModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="ti tabler-history me-2"></i>{{ __('Historial de Versiones') }}</h5>
                        <button type="button" wire:click="closeModal" class="btn-close"></button>
                    </div>
                    <div class="modal-body">
                        @if ($this->versionesHistorial->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('Versión') }}</th>
                                            <th>{{ __('Fecha') }}</th>
                                            <th>{{ __('Motivo') }}</th>
                                            <th>{{ __('Docs Generados') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($this->versionesHistorial as $ver)
                                            <tr>
                                                <td><span class="badge bg-label-primary">v{{ $ver->version }}</span></td>
                                                <td>{{ $ver->fecha_version ? \Carbon\Carbon::parse($ver->fecha_version)->format('d/m/Y H:i') : '-' }}</td>
                                                <td>{{ $ver->motivo_cambio ?? '-' }}</td>
                                                <td class="text-center">{{ $ver->documentos_generados }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="ti tabler-history-off icon-48px text-muted mb-3 d-block"></i>
                                <p class="text-muted">{{ __('No hay versiones registradas para esta plantilla.') }}</p>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button wire:click="closeModal" class="btn btn-outline-secondary">{{ __('Cerrar') }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
