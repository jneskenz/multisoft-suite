<div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-12 col-md-5">
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="ti tabler-search"></i></span>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        class="form-control"
                        placeholder="{{ __('Buscar por nombre, email, documento o código...') }}"
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

            <div class="col-6 col-md-3">
                <select wire:model.live="statusFilter" class="form-select">
                    <option value="">{{ __('Todos') }}</option>
                    <option value="active">{{ __('Activos') }}</option>
                    <option value="suspended">{{ __('Suspendidos') }}</option>
                    <option value="cesado">{{ __('Cesados') }}</option>
                </select>
            </div>

            <div class="col-6 col-md-2">
                <select wire:model.live="perPage" class="form-select">
                    <option value="10">10 por pagina</option>
                    <option value="25">25 por pagina</option>
                    <option value="50">50 por pagina</option>
                </select>
            </div>

            <div class="col-12 col-md-2 d-flex gap-2">
                @if ($search || $statusFilter)
                    <button wire:click="clearFilters" class="btn btn-outline-secondary flex-fill" title="{{ __('Limpiar filtros') }}">
                        <i class="ti tabler-filter-off"></i>
                    </button>
                @endif
                <button
                    wire:click="toggleTrashedEmpleados"
                    class="btn {{ $showTrashedEmpleados ? 'btn-danger' : 'btn-outline-secondary' }}"
                    title="{{ $showTrashedEmpleados ? __('Ver empleados activos') : __('Ver empleados eliminados') }}"
                >
                    <i class="ti {{ $showTrashedEmpleados ? 'tabler-users' : 'tabler-trash' }}"></i>
                </button>
            </div>

            @if ($showTrashedEmpleados)
                <div class="col-12">
                    <div class="alert alert-warning d-flex align-items-center mb-0" role="alert">
                        <i class="ti tabler-trash me-2"></i>
                        <span>{{ __('Mostrando empleados eliminados. Puedes restaurarlos o eliminarlos permanentemente.') }}</span>
                    </div>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 130px;">{{ __('Acciones') }}</th>
                            <th style="width: 120px;">{{ __('Estado') }}</th>
                            <th wire:click="sortBy('nombre')" class="cursor-pointer" style="min-width: 200px;">
                                <div class="d-flex align-items-center">
                                    {{ __('Empleado') }}
                                    @if ($sortField === 'nombre')
                                        <i class="ti tabler-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </div>
                            </th>
                            <th style="min-width: 120px;">{{ __('Código') }}</th>
                            <th style="min-width: 140px;">{{ __('Documento') }}</th>
                            <th style="min-width: 150px;">{{ __('Cargo') }}</th>
                            <th style="min-width: 180px;">{{ __('Empresa') }}</th>
                            <th style="width: 80px;" class="text-center">{{ __('Acceso') }}</th>
                            <th wire:click="sortBy('created_at')" class="cursor-pointer" style="width: 160px;">
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
                        @forelse($this->empleados as $empleado)
                            <tr wire:key="empleado-{{ $empleado->id }}">
                                <td>
                                    <div class="d-flex justify-content-start align-items-center gap-1">
                                        <button
                                            class="btn btn-sm rounded-pill btn-icon btn-label-secondary waves-effect me-1"
                                            data-bs-toggle="tooltip"
                                            title="Nro de registro: {{ $empleado->id }}"
                                        >
                                            {{ $empleado->id }}
                                        </button>

                                        <button
                                            wire:click="edit({{ $empleado->id }})"
                                            class="btn btn-sm btn-icon btn-label-primary"
                                            data-bs-toggle="tooltip"
                                            title="{{ __('Editar empleado') }}"
                                        >
                                            <i class="ti tabler-edit icon-18px"></i>
                                        </button>

                                        <a href="{{ route('hr.empleados.show', $empleado->id) }}"
                                            wire:click="show({{ $empleado->id }})"
                                            class="btn btn-sm btn-icon btn-label-primary"
                                            data-bs-toggle="tooltip"
                                            title="{{ __('Ver ficha del empleado') }}"
                                        >
                                            <i class="ti tabler-user-square-rounded icon-18px"></i>
                                        </a>

                                        <div class="dropdown">
                                            <button
                                                class="btn btn-sm btn-icon border btn-text-secondary rounded dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown"
                                            >
                                                <i class="ti tabler-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                @if (!$showTrashedEmpleados)
                                                    @if ((int) $empleado->estado === \Modules\HR\Models\Empleado::ESTADO_ACTIVO)
                                                        <li>
                                                            <a
                                                                class="dropdown-item d-flex p-2 justify-content-start btn btn-label-warning align-items-center"
                                                                wire:click="suspend({{ $empleado->id }})"
                                                            >
                                                                <button class="btn btn-sm btn-icon btn-label-warning me-2">
                                                                    <i class="ti tabler-ban icon-18px"></i>
                                                                </button>
                                                                <span class="lh-1">{{ __('Suspender') }}</span>
                                                            </a>
                                                        </li>
                                                    @elseif ((int) $empleado->estado === \Modules\HR\Models\Empleado::ESTADO_SUSPENDIDO)
                                                        <li>
                                                            <a
                                                                class="dropdown-item d-flex p-2 justify-content-start btn btn-label-success align-items-center"
                                                                wire:click="activate({{ $empleado->id }})"
                                                            >
                                                                <button class="btn btn-sm btn-icon btn-label-success me-2">
                                                                    <i class="ti tabler-check icon-18px"></i>
                                                                </button>
                                                                <span class="lh-1">{{ __('Activar') }}</span>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a
                                                                class="dropdown-item d-flex p-2 justify-content-start btn btn-label-dark align-items-center"
                                                                wire:click="cesar({{ $empleado->id }})"
                                                            >
                                                                <button class="btn btn-sm btn-icon btn-label-dark me-2">
                                                                    <i class="ti tabler-user-x icon-18px"></i>
                                                                </button>
                                                                <span class="lh-1">{{ __('Marcar como cesado') }}</span>
                                                            </a>
                                                        </li>
                                                    @else
                                                        <li>
                                                            <a
                                                                class="dropdown-item d-flex p-2 justify-content-start btn btn-label-success align-items-center"
                                                                wire:click="activate({{ $empleado->id }})"
                                                            >
                                                                <button class="btn btn-sm btn-icon btn-label-success me-2">
                                                                    <i class="ti tabler-check icon-18px"></i>
                                                                </button>
                                                                <span class="lh-1">{{ __('Activar') }}</span>
                                                            </a>
                                                        </li>
                                                    @endif
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a
                                                            class="dropdown-item d-flex p-2 justify-content-start btn btn-label-danger align-items-center"
                                                            wire:click="confirmDelete({{ $empleado->id }})"
                                                        >
                                                            <button class="btn btn-sm btn-icon btn-label-danger me-2">
                                                                <i class="ti tabler-trash icon-18px"></i>
                                                            </button>
                                                            <span class="lh-1">{{ __('Eliminar') }}</span>
                                                        </a>
                                                    </li>
                                                @else
                                                    <li>
                                                        <a
                                                            class="dropdown-item d-flex p-2 justify-content-start btn btn-label-success align-items-center"
                                                            wire:click="confirmRestore({{ $empleado->id }})"
                                                        >
                                                            <button class="btn btn-sm btn-icon btn-label-success me-2">
                                                                <i class="ti tabler-refresh icon-18px"></i>
                                                            </button>
                                                            <span class="lh-1">{{ __('Restaurar') }}</span>
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a
                                                            class="dropdown-item d-flex p-2 justify-content-start btn btn-label-dark align-items-center"
                                                            wire:click="confirmForceDelete({{ $empleado->id }})"
                                                        >
                                                            <button class="btn btn-sm btn-icon btn-label-dark me-2">
                                                                <i class="ti tabler-trash-x icon-18px"></i>
                                                            </button>
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
                                        $estadoBadge = match((int) $empleado->estado) {
                                            \Modules\HR\Models\Empleado::ESTADO_ACTIVO => ['class' => 'bg-label-success', 'text' => __('Activo')],
                                            \Modules\HR\Models\Empleado::ESTADO_SUSPENDIDO => ['class' => 'bg-label-warning', 'text' => __('Suspendido')],
                                            \Modules\HR\Models\Empleado::ESTADO_CESADO => ['class' => 'bg-label-dark', 'text' => __('Cesado')],
                                            default => ['class' => 'bg-label-secondary', 'text' => __('Desconocido')],
                                        };
                                    @endphp
                                    <span class="badge {{ $estadoBadge['class'] }}">
                                        {{ $estadoBadge['text'] }}
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <h6 class="mb-0">{{ $empleado->nombre }}</h6>
                                        @if($empleado->email)
                                            <small class="text-muted">{{ $empleado->email }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($empleado->codigo_empleado)
                                        <span class="badge bg-label-info">{{ $empleado->codigo_empleado }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($empleado->documento_numero)
                                        <div>
                                            @if($empleado->documento_tipo)
                                                <small class="text-muted">{{ $empleado->documento_tipo }}:</small>
                                            @endif
                                            <span>{{ $empleado->documento_numero }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $empleado->cargo ?? '-' }}
                                </td>
                                <td>
                                    @if($empleado->company)
                                        <div>
                                            <span>{{ $empleado->company->name }}</span>
                                            @if($empleado->location)
                                                <br><small class="text-muted">{{ $empleado->location->name }}</small>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($empleado->user_id)
                                        <span class="badge bg-label-success" title="{{ __('Tiene acceso al sistema') }}">
                                            <i class="ti tabler-check"></i>
                                        </span>
                                    @else
                                        <span class="badge bg-label-secondary" title="{{ __('Sin acceso al sistema') }}">
                                            <i class="ti tabler-x"></i>
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span title="{{ optional($empleado->created_at)->format('d/m/Y H:i') }}">
                                        {{ optional($empleado->created_at)->diffForHumans() }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="ti tabler-users-minus icon-48px text-muted mb-3"></i>
                                        <h6 class="mb-1">{{ __('No se encontraron empleados') }}</h6>
                                        <p class="text-muted mb-0">
                                            @if ($search || $statusFilter)
                                                {{ __('Intenta ajustar los filtros de busqueda') }}
                                            @else
                                                {{ __('Aun no hay empleados registrados') }}
                                            @endif
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($this->empleados->hasPages())
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        {{ __('Mostrando') }} {{ $this->empleados->firstItem() }} - {{ $this->empleados->lastItem() }}
                        {{ __('de') }} {{ $this->empleados->total() }} {{ __('empleados') }}
                    </div>
                    {{ $this->empleados->links() }}
                </div>
            @endif
        </div>
    </div>

    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="ti tabler-{{ $isEditing ? 'pencil' : 'user-plus' }} me-2"></i>
                            {{ $isEditing ? __('Editar Empleado') : __('Nuevo Empleado') }}
                        </h5>
                        <button type="button" wire:click="closeModal" class="btn-close"></button>
                    </div>

                    <form wire:submit="save">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label" for="empleado_nombre">
                                        {{ __('Nombre') }} <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="ti tabler-user"></i></span>
                                        <input
                                            type="text"
                                            id="empleado_nombre"
                                            wire:model="nombre"
                                            class="form-control @error('nombre') is-invalid @enderror"
                                            placeholder="{{ __('Nombre completo') }}"
                                            autofocus
                                        >
                                    </div>
                                    @error('nombre')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label" for="empleado_email">
                                        {{ __('Correo electrónico') }}
                                    </label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="ti tabler-mail"></i></span>
                                        <input
                                            type="email"
                                            id="empleado_email"
                                            wire:model="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            placeholder="{{ __('correo@empresa.com') }}"
                                        >
                                    </div>
                                    @error('email')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">{{ __('Email de contacto (opcional)') }}</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="empleado_documento_tipo">
                                        {{ __('Tipo de documento') }}
                                    </label>
                                    <select id="empleado_documento_tipo" wire:model="documento_tipo" class="form-select @error('documento_tipo') is-invalid @enderror">
                                        <option value="">{{ __('Seleccionar...') }}</option>
                                        <option value="DNI">DNI</option>
                                        <option value="CE">Carnet de Extranjería</option>
                                        <option value="Pasaporte">Pasaporte</option>
                                        <option value="RUC">RUC</option>
                                    </select>
                                    @error('documento_tipo')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="empleado_documento_numero">
                                        {{ __('Número de documento') }}
                                    </label>
                                    <input
                                        type="text"
                                        id="empleado_documento_numero"
                                        wire:model="documento_numero"
                                        class="form-control @error('documento_numero') is-invalid @enderror"
                                        placeholder="{{ __('Ej: 12345678') }}"
                                    >
                                    @error('documento_numero')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="empleado_telefono">
                                        {{ __('Teléfono') }}
                                    </label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="ti tabler-phone"></i></span>
                                        <input
                                            type="tel"
                                            id="empleado_telefono"
                                            wire:model="telefono"
                                            class="form-control @error('telefono') is-invalid @enderror"
                                            placeholder="{{ __('Ej: +51 999 999 999') }}"
                                        >
                                    </div>
                                    @error('telefono')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="empleado_codigo_empleado">
                                        {{ __('Código de empleado') }}
                                    </label>
                                    <input
                                        type="text"
                                        id="empleado_codigo_empleado"
                                        wire:model="codigo_empleado"
                                        class="form-control @error('codigo_empleado') is-invalid @enderror"
                                        placeholder="{{ __('Ej: EMP-001') }}"
                                    >
                                    @error('codigo_empleado')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="empleado_cargo">
                                        {{ __('Cargo') }}
                                    </label>
                                    <input
                                        type="text"
                                        id="empleado_cargo"
                                        wire:model="cargo"
                                        class="form-control @error('cargo') is-invalid @enderror"
                                        placeholder="{{ __('Ej: Gerente de Ventas') }}"
                                    >
                                    @error('cargo')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="empleado_fecha_ingreso">
                                        {{ __('Fecha de ingreso') }}
                                    </label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="ti tabler-calendar"></i></span>
                                        <input
                                            type="date"
                                            id="empleado_fecha_ingreso"
                                            wire:model="fecha_ingreso"
                                            class="form-control @error('fecha_ingreso') is-invalid @enderror"
                                        >
                                    </div>
                                    @error('fecha_ingreso')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="empleado_company_id">
                                        {{ __('Empresa') }} <span class="text-danger">*</span>
                                    </label>
                                    <select id="empleado_company_id" wire:model.live="company_id" class="form-select @error('company_id') is-invalid @enderror">
                                        <option value="">{{ __('Seleccionar empresa...') }}</option>
                                        @foreach($this->availableCompanies as $company)
                                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('company_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="empleado_location_id">
                                        {{ __('Local') }}
                                    </label>
                                    <select id="empleado_location_id" wire:model="location_id" class="form-select @error('location_id') is-invalid @enderror" @if(!$company_id) disabled @endif>
                                        <option value="">{{ __('Seleccionar local...') }}</option>
                                        @foreach($this->availableLocations as $location)
                                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('location_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    @if(!$company_id)
                                        <small class="text-muted">{{ __('Primero selecciona una empresa') }}</small>
                                    @endif
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label" for="empleado_estado">{{ __('Estado') }}</label>
                                    <select id="empleado_estado" wire:model="estado" class="form-select @error('estado') is-invalid @enderror">
                                        <option value="1">{{ __('Activo') }}</option>
                                        <option value="0">{{ __('Suspendido') }}</option>
                                        <option value="2">{{ __('Cesado') }}</option>
                                    </select>
                                    @error('estado')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" wire:click="closeModal" class="btn btn-outline-secondary">
                                {{ __('Cancelar') }}
                            </button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="save">
                                    <i class="ti tabler-device-floppy me-1"></i>
                                    {{ $isEditing ? __('Actualizar') : __('Crear Empleado') }}
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

    @if ($showDeleteModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center py-4">
                        <div class="mb-3">
                            <i class="ti tabler-alert-triangle icon-48px text-warning"></i>
                        </div>
                        <h5>{{ __('Eliminar empleado?') }}</h5>
                        <p class="text-muted mb-4">
                            {{ __('El empleado sera movido a la papelera y podra ser restaurado posteriormente.') }}
                        </p>
                        <div class="d-flex justify-content-center gap-2">
                            <button wire:click="closeModal" class="btn btn-outline-secondary">
                                {{ __('Cancelar') }}
                            </button>
                            <button wire:click="delete" class="btn btn-danger" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="delete">
                                    <i class="ti tabler-trash me-1"></i>
                                    {{ __('Eliminar') }}
                                </span>
                                <span wire:loading wire:target="delete">
                                    <span class="spinner-border spinner-border-sm"></span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($showRestoreModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center py-4">
                        <div class="mb-3">
                            <i class="ti tabler-refresh icon-48px text-success"></i>
                        </div>
                        <h5>{{ __('Restaurar empleado?') }}</h5>
                        <p class="text-muted mb-4">
                            {{ __('El empleado sera restaurado y volvera a estar disponible.') }}
                        </p>
                        <div class="d-flex justify-content-center gap-2">
                            <button wire:click="closeModal" class="btn btn-outline-secondary">
                                {{ __('Cancelar') }}
                            </button>
                            <button wire:click="restore" class="btn btn-success" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="restore">
                                    <i class="ti tabler-refresh me-1"></i>
                                    {{ __('Restaurar') }}
                                </span>
                                <span wire:loading wire:target="restore">
                                    <span class="spinner-border spinner-border-sm"></span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($showForceDeleteModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center py-4">
                        <div class="mb-3">
                            <i class="ti tabler-trash-x icon-48px text-danger"></i>
                        </div>
                        <h5>{{ __('Eliminar permanentemente?') }}</h5>
                        <p class="text-muted mb-4">
                            {{ __('Esta accion no se puede deshacer. Todos los datos del empleado seran eliminados.') }}
                        </p>
                        <div class="d-flex justify-content-center gap-2">
                            <button wire:click="closeModal" class="btn btn-outline-secondary">
                                {{ __('Cancelar') }}
                            </button>
                            <button wire:click="forceDelete" class="btn btn-danger" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="forceDelete">
                                    <i class="ti tabler-trash-x me-1"></i>
                                    {{ __('Eliminar') }}
                                </span>
                                <span wire:loading wire:target="forceDelete">
                                    <span class="spinner-border spinner-border-sm"></span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
