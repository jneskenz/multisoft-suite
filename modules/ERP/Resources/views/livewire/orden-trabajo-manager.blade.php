<div>
    <div class="card-body">
        @if(!$this->hasOrdenesTrabajoTable)
            <div class="alert alert-warning mb-0">
                <i class="ti tabler-alert-triangle me-2"></i>
                {{ __('La tabla erp_ordenes_trabajo aun no existe. Ejecuta migraciones para visualizar datos.') }}
            </div>
        @else
            @php
                $stats = $this->stats;
                $ordenes = $this->ordenesTrabajo;
            @endphp

            <div class="row g-6 mb-6">
                <div class="col-xl-3 col-sm-6">
                    <div class="card card-border-shadow-primary h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted">{{ __('Ordenes hoy') }}</span>
                                <i class="ti tabler-clipboard-list text-primary"></i>
                            </div>
                            <h4 class="mb-0">{{ number_format((int) $stats['hoy']) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="card card-border-shadow-warning h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted">{{ __('Pendientes') }}</span>
                                <i class="ti tabler-hourglass text-warning"></i>
                            </div>
                            <h4 class="mb-0">{{ number_format((int) $stats['pendiente']) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="card card-border-shadow-info h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted">{{ __('En laboratorio') }}</span>
                                <i class="ti tabler-flask text-info"></i>
                            </div>
                            <h4 class="mb-0">{{ number_format((int) $stats['en_laboratorio']) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="card card-border-shadow-success h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted">{{ __('Listas') }}</span>
                                <i class="ti tabler-checkup-list text-success"></i>
                            </div>
                            <h4 class="mb-0">{{ number_format((int) $stats['listo']) }}</h4>
                            <small class="text-muted">{{ __('Total') }}: {{ number_format((int) $stats['total']) }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 align-items-end mb-4">
                <div class="col-12 col-md-2">
                    <label class="form-label">{{ __('Nro OT') }}</label>
                    <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="ti tabler-hash"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="ordenFilter" class="form-control" placeholder="OT-PE-000001">
                    </div>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">{{ __('Paciente') }}</label>
                    <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="ti tabler-user-search"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="patientFilter" class="form-control" placeholder="{{ __('Nombre o documento') }}" list="ordenes-trabajo-livewire-patients" autocomplete="off">
                    </div>
                    <datalist id="ordenes-trabajo-livewire-patients">
                        @foreach($this->patientOptions as $patient)
                            <option value="{{ $patient['label'] }}"></option>
                        @endforeach
                    </datalist>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label">{{ __('Estado') }}</label>
                    <select wire:model.live="estadoFilter" class="form-select">
                        <option value="">{{ __('Todos') }}</option>
                        <option value="pendiente">{{ __('Pendiente') }}</option>
                        <option value="en_laboratorio">{{ __('En laboratorio') }}</option>
                        <option value="control_calidad">{{ __('Control de calidad') }}</option>
                        <option value="listo">{{ __('Listo') }}</option>
                        <option value="entregado">{{ __('Entregado') }}</option>
                        <option value="anulado">{{ __('Anulado') }}</option>
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label">{{ __('Prioridad') }}</label>
                    <select wire:model.live="prioridadFilter" class="form-select">
                        <option value="">{{ __('Todas') }}</option>
                        <option value="normal">{{ __('Normal') }}</option>
                        <option value="urgente">{{ __('Urgente') }}</option>
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label">{{ __('Fecha') }}</label>
                    <input type="date" wire:model.live="fechaFilter" class="form-control">
                </div>

                <div class="col-6 col-md-1">
                    <label class="form-label">{{ __('Por pagina') }}</label>
                    <select wire:model.live="perPage" class="form-select">
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th wire:click="sortBy('numero_ot')" class="cursor-pointer">{{ __('OT') }}</th>
                            <th wire:click="sortBy('fecha_orden')" class="cursor-pointer">{{ __('Fecha') }}</th>
                            <th>{{ __('Paciente') }}</th>
                            <th>{{ __('Ticket / Receta') }}</th>
                            <th>{{ __('Items') }}</th>
                            <th wire:click="sortBy('prioridad')" class="cursor-pointer">{{ __('Prioridad') }}</th>
                            <th wire:click="sortBy('estado_ot')" class="cursor-pointer">{{ __('Estado') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ordenes as $orden)
                            @php
                                $estado = $estadoMeta[$orden->estado_ot] ?? ['label' => ucfirst((string) $orden->estado_ot), 'class' => 'bg-label-secondary'];
                                $prioridad = $prioridadMeta[$orden->prioridad] ?? ['label' => ucfirst((string) $orden->prioridad), 'class' => 'bg-label-secondary'];
                                $pacienteNombre = trim((string) ($orden->paciente?->nombre_completo ?: implode(' ', array_filter([
                                    $orden->paciente?->nombres,
                                    $orden->paciente?->apellido_paterno,
                                    $orden->paciente?->apellido_materno,
                                ]))));
                                $pacienteDocumento = trim(implode(' ', array_filter([
                                    $orden->paciente?->tipo_documento,
                                    $orden->paciente?->numero_documento,
                                ])));
                            @endphp
                            <tr wire:key="orden-trabajo-{{ $orden->id }}">
                                <td>
                                    <div class="fw-medium">{{ $orden->numero_ot }}</div>
                                    @if($orden->fecha_prometida)
                                        <small class="text-muted">{{ __('Prometida') }}: {{ optional($orden->fecha_prometida)->format('Y-m-d H:i') }}</small>
                                    @endif
                                </td>
                                <td>{{ optional($orden->fecha_orden)->format('Y-m-d H:i') ?? '-' }}</td>
                                <td>
                                    <div>{{ $pacienteNombre !== '' ? $pacienteNombre : __('Sin paciente') }}</div>
                                    @if($pacienteDocumento !== '')
                                        <small class="text-muted">{{ $pacienteDocumento }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ __('Ticket') }}: {{ $orden->ticket?->ticket_numero ?? '-' }}</div>
                                    <small class="text-muted">{{ __('Receta') }}: {{ $orden->receta?->receta_numero ?? '-' }}</small>
                                </td>
                                <td>{{ number_format((int) $orden->detalles_count) }}</td>
                                <td><span class="badge {{ $prioridad['class'] }}">{{ __($prioridad['label']) }}</span></td>
                                <td><span class="badge {{ $estado['class'] }}">{{ __($estado['label']) }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center text-muted">
                                        <i class="ti tabler-search icon-48px mb-2"></i>
                                        <h6 class="mb-1">{{ __('No se encontraron ordenes de trabajo') }}</h6>
                                        <p class="mb-0">
                                            @if($this->hasActiveFilters)
                                                {{ __('Intenta ajustar los filtros de busqueda.') }}
                                            @else
                                                {{ __('Aun no hay ordenes de trabajo registradas.') }}
                                            @endif
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($ordenes->hasPages())
                <div class="card-footer d-flex flex-wrap justify-content-between align-items-center gap-2 mt-4">
                    <small class="text-muted">{{ __('Mostrando') }} {{ $ordenes->firstItem() ?? 0 }} - {{ $ordenes->lastItem() ?? 0 }} {{ __('de') }} {{ $ordenes->total() ?? 0 }} {{ __('ordenes') }}</small>
                    {{ $ordenes->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
