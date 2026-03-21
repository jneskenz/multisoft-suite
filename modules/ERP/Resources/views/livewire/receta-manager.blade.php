<div>
    <div class="card-body">
        @if(!$this->hasRecetasTable)
            <div class="alert alert-warning mb-0">
                <i class="ti tabler-alert-triangle me-2"></i>
                {{ __('La tabla erp_recetas aun no existe. Ejecuta migraciones para visualizar datos.') }}
            </div>
        @else
            @php
                $stats = $this->stats;
                $recetas = $this->recetas;
            @endphp

            <div class="row g-6 mb-6">
                <div class="col-xl-3 col-sm-6">
                    <div class="card card-border-shadow-primary h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted">{{ __('Recetas hoy') }}</span>
                                <i class="ti tabler-file-certificate text-primary"></i>
                            </div>
                            <h4 class="mb-0">{{ number_format((int) $stats['hoy']) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="card card-border-shadow-warning h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted">{{ __('Borrador') }}</span>
                                <i class="ti tabler-edit text-warning"></i>
                            </div>
                            <h4 class="mb-0">{{ number_format((int) $stats['borrador']) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="card card-border-shadow-success h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted">{{ __('Emitidas') }}</span>
                                <i class="ti tabler-checkup-list text-success"></i>
                            </div>
                            <h4 class="mb-0">{{ number_format((int) $stats['emitida']) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="card card-border-shadow-danger h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted">{{ __('Anuladas') }}</span>
                                <i class="ti tabler-ban text-danger"></i>
                            </div>
                            <h4 class="mb-0">{{ number_format((int) $stats['anulada']) }}</h4>
                            <small class="text-muted">{{ __('Total') }}: {{ number_format((int) $stats['total']) }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 align-items-end mb-4">
                <div class="col-12 col-md-2">
                    <label class="form-label">{{ __('Nro receta') }}</label>
                    <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="ti tabler-hash"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="recetaFilter" class="form-control" placeholder="RC-PE-000001">
                    </div>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">{{ __('Paciente') }}</label>
                    <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="ti tabler-user-search"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="patientFilter" class="form-control" placeholder="{{ __('Nombre o documento') }}" list="recetas-livewire-patients" autocomplete="off">
                    </div>
                    <datalist id="recetas-livewire-patients">
                        @foreach($this->patientOptions as $patient)
                            <option value="{{ $patient['label'] }}"></option>
                        @endforeach
                    </datalist>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label">{{ __('Estado') }}</label>
                    <select wire:model.live="estadoFilter" class="form-select">
                        <option value="">{{ __('Todos') }}</option>
                        <option value="borrador">{{ __('Borrador') }}</option>
                        <option value="emitida">{{ __('Emitida') }}</option>
                        <option value="cerrada">{{ __('Cerrada') }}</option>
                        <option value="anulada">{{ __('Anulada') }}</option>
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
                            <th class="text-center" style="width: 130px;">{{ __('Acciones') }}</th>
                            <th wire:click="sortBy('receta_numero')" class="cursor-pointer">{{ __('Receta') }}</th>
                            <th wire:click="sortBy('fecha_receta')" class="cursor-pointer">{{ __('Fecha') }}</th>
                            <th>{{ __('Paciente') }}</th>
                            <th>{{ __('Ticket') }}</th>
                            <th>{{ __('Especialista') }}</th>
                            <th wire:click="sortBy('estado_receta')" class="cursor-pointer">{{ __('Estado') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recetas as $receta)
                            @php
                                $meta = $estadoMeta[$receta->estado_receta] ?? ['label' => ucfirst((string) $receta->estado_receta), 'class' => 'bg-label-secondary'];
                                $pacienteNombre = trim((string) ($receta->paciente?->nombre_completo ?: implode(' ', array_filter([
                                    $receta->paciente?->nombres,
                                    $receta->paciente?->apellido_paterno,
                                    $receta->paciente?->apellido_materno,
                                ]))));
                                $pacienteDocumento = trim(implode(' ', array_filter([
                                    $receta->paciente?->tipo_documento,
                                    $receta->paciente?->numero_documento,
                                ])));
                            @endphp
                            <tr wire:key="receta-{{ $receta->id }}">
                                <td class="text-center">
                                    <a
                                        href="{{ group_route('erp.recetas.detalle', [
                                            'locale' => request()->route('locale'),
                                            'group' => request()->route('group'),
                                            'receta' => $receta->id,
                                        ]) }}"
                                        class="btn btn-sm btn-text-info"
                                        title="{{ __('Detalle clinico') }}"
                                    >
                                        <i class="ti tabler-stethoscope"></i>
                                    </a>
                                    <a
                                        href="{{ group_route('erp.recetas.edit', [
                                            'locale' => request()->route('locale'),
                                            'group' => request()->route('group'),
                                            'receta' => $receta->id,
                                        ]) }}"
                                        class="btn btn-sm btn-text-primary"
                                        title="{{ __('Editar receta') }}"
                                    >
                                        <i class="ti tabler-edit"></i>
                                    </a>
                                </td>
                                <td><div class="fw-medium">{{ $receta->receta_numero }}</div></td>
                                <td>{{ optional($receta->fecha_receta)->format('Y-m-d H:i') ?? '-' }}</td>
                                <td>
                                    <div>{{ $pacienteNombre !== '' ? $pacienteNombre : __('Sin paciente') }}</div>
                                    @if($pacienteDocumento !== '')
                                        <small class="text-muted">{{ $pacienteDocumento }}</small>
                                    @endif
                                </td>
                                <td>{{ $receta->ticket?->ticket_numero ?? '-' }}</td>
                                <td>{{ $receta->especialista?->name ?? '-' }}</td>
                                <td><span class="badge {{ $meta['class'] }}">{{ __($meta['label']) }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center text-muted">
                                        <i class="ti tabler-search icon-48px mb-2"></i>
                                        <h6 class="mb-1">{{ __('No se encontraron recetas') }}</h6>
                                        <p class="mb-0">
                                            @if($this->hasActiveFilters)
                                                {{ __('Intenta ajustar los filtros de busqueda.') }}
                                            @else
                                                {{ __('Aun no hay recetas registradas.') }}
                                            @endif
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($recetas->hasPages())
                <div class="card-footer d-flex flex-wrap justify-content-between align-items-center gap-2 mt-4">
                    <small class="text-muted">{{ __('Mostrando') }} {{ $recetas->firstItem() ?? 0 }} - {{ $recetas->lastItem() ?? 0 }} {{ __('de') }} {{ $recetas->total() ?? 0 }} {{ __('recetas') }}</small>
                    {{ $recetas->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
