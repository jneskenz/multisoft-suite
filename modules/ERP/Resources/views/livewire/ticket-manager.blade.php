<div>
    <div class="card-body">
        @if(!$this->hasTicketsTable)
            <div class="alert alert-warning mb-0">
                <i class="ti tabler-alert-triangle me-2"></i>
                {{ __('La tabla erp_tickets aun no existe. Ejecuta migraciones para visualizar datos.') }}
            </div>
        @else
            @php
                $stats = $this->stats;
                $tickets = $this->tickets;
            @endphp

            <div class="row g-6 mb-6">
                <div class="col-xl-3 col-sm-6">
                    <div class="card card-border-shadow-primary h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted">{{ __('Tickets hoy') }}</span>
                                <i class="ti tabler-ticket text-primary"></i>
                            </div>
                            <h4 class="mb-0">{{ number_format((int) $stats['hoy']) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="card card-border-shadow-warning h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted">{{ __('Abiertos') }}</span>
                                <i class="ti tabler-clock-hour-4 text-warning"></i>
                            </div>
                            <h4 class="mb-0">{{ number_format((int) $stats['abiertos']) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="card card-border-shadow-success h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted">{{ __('En proceso') }}</span>
                                <i class="ti tabler-progress text-success"></i>
                            </div>
                            <h4 class="mb-0">{{ number_format((int) $stats['en_proceso']) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="card card-border-shadow-info h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted">{{ __('Cerrados') }}</span>
                                <i class="ti tabler-check text-info"></i>
                            </div>
                            <h4 class="mb-0">{{ number_format((int) $stats['cerrados']) }}</h4>
                            <small class="text-muted">{{ __('Total') }}: {{ number_format((int) $stats['total']) }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 align-items-end mb-4">
                <div class="col-12 col-md-3">
                    <label class="form-label">{{ __('Nro ticket') }}</label>
                    <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="ti tabler-hash"></i></span>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="ticketFilter"
                            class="form-control"
                            placeholder="TK-PE-000123"
                        >
                        <span class="input-group-text cursor-pointer">
                            <div wire:loading wire:target="ticketFilter" class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">{{ __('Buscando...') }}</span>
                            </div>
                            @if(trim($ticketFilter) !== '')
                                <i class="ti tabler-x" wire:click="$set('ticketFilter', '')"></i>
                            @endif
                        </span>
                    </div>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">{{ __('Paciente') }}</label>
                    <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="ti tabler-user-search"></i></span>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="patientFilter"
                            class="form-control"
                            placeholder="{{ __('Nombre o documento') }}"
                            list="tickets-livewire-patients"
                            autocomplete="off"
                        >
                        <span class="input-group-text cursor-pointer">
                            <div wire:loading wire:target="patientFilter" class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">{{ __('Buscando...') }}</span>
                            </div>
                            @if(trim($patientFilter) !== '')
                                <i class="ti tabler-x" wire:click="$set('patientFilter', '')"></i>
                            @endif
                        </span>
                    </div>
                    <datalist id="tickets-livewire-patients">
                        @foreach($this->patientOptions as $patient)
                            <option value="{{ $patient['label'] }}"></option>
                        @endforeach
                    </datalist>
                    {{-- <small class="text-muted">{{ __('Escribe al menos 2 caracteres para sugerencias') }}</small> --}}
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label">{{ __('Estado') }}</label>
                    <select wire:model.live="estadoFilter" class="form-select">
                        <option value="">{{ __('Todos') }}</option>
                        <option value="abierto">{{ __('Abierto') }}</option>
                        <option value="en_proceso">{{ __('En proceso') }}</option>
                        <option value="listo">{{ __('Listo') }}</option>
                        <option value="cerrado">{{ __('Cerrado') }}</option>
                        <option value="anulado">{{ __('Anulado') }}</option>
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

                <div class="col-6 col-md-1 d-flex gap-1">
                    @if($this->hasActiveFilters)
                        <button wire:click="clearFilters" class="btn btn-outline-secondary w-100" title="{{ __('Limpiar filtros') }}">
                            <i class="ti tabler-filter-off"></i>
                        </button>
                    @endif
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 90px;">{{ __('Acciones') }}</th>
                            <th wire:click="sortBy('ticket_numero')" class="cursor-pointer">
                                <div class="d-flex align-items-center">
                                    {{ __('Ticket') }}
                                    @if($sortField === 'ticket_numero')
                                        <i class="ti tabler-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </div>
                            </th>
                            <th wire:click="sortBy('fecha_ticket')" class="cursor-pointer">
                                <div class="d-flex align-items-center">
                                    {{ __('Fecha') }}
                                    @if($sortField === 'fecha_ticket')
                                        <i class="ti tabler-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </div>
                            </th>
                            <th>{{ __('Paciente') }}</th>
                            <th wire:click="sortBy('estado_ticket')" class="cursor-pointer">
                                <div class="d-flex align-items-center">
                                    {{ __('Estado') }}
                                    @if($sortField === 'estado_ticket')
                                        <i class="ti tabler-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </div>
                            </th>
                            <th wire:click="sortBy('prioridad')" class="cursor-pointer">
                                <div class="d-flex align-items-center">
                                    {{ __('Responsable') }}
                                    @if($sortField === 'prioridad')
                                        <i class="ti tabler-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </div>
                            </th>
                            <th wire:click="sortBy('total')" class="text-end cursor-pointer">
                                <div class="d-flex align-items-center justify-content-end">
                                    {{ __('Total') }}
                                    @if($sortField === 'total')
                                        <i class="ti tabler-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </div>
                            </th>
                            <th wire:click="sortBy('saldo_pendiente')" class="text-end cursor-pointer">
                                <div class="d-flex align-items-center justify-content-end">
                                    {{ __('Saldo') }}
                                    @if($sortField === 'saldo_pendiente')
                                        <i class="ti tabler-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                            @php
                                $meta = $estadoMeta[$ticket->estado_ticket] ?? ['label' => ucfirst((string) $ticket->estado_ticket), 'class' => 'bg-label-secondary'];
                                $pacienteNombre = trim((string) ($ticket->paciente?->nombre_completo ?: implode(' ', array_filter([
                                    $ticket->paciente?->nombres,
                                    $ticket->paciente?->apellido_paterno,
                                    $ticket->paciente?->apellido_materno,
                                ]))));
                                $pacienteDocumento = trim(implode(' ', array_filter([
                                    $ticket->paciente?->tipo_documento,
                                    $ticket->paciente?->numero_documento,
                                ])));
                            @endphp
                            <tr wire:key="ticket-{{ $ticket->id }}">
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-text-secondary" title="{{ __('Ver ticket') }}">
                                        <i class="ti tabler-eye"></i>
                                    </button>
                                </td>
                                <td>
                                    <div class="fw-medium">{{ $ticket->ticket_numero }}</div>
                                    <small class="text-muted">{{ strtoupper((string) ($ticket->canal ?? 'mostrador')) }}</small>
                                </td>
                                <td>
                                    {{ optional($ticket->fecha_ticket)->format('Y-m-d H:i') ?? '-' }}
                                </td>
                                <td>
                                    <div>{{ $pacienteNombre !== '' ? $pacienteNombre : __('Sin paciente') }}</div>
                                    @if($pacienteDocumento !== '')
                                        <small class="text-muted">{{ $pacienteDocumento }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $meta['class'] }}">{{ __($meta['label']) }}</span>
                                </td>
                                <td>
                                    <div>{{ $ticket->creadoPor?->name ?? __('Sistema') }}</div>
                                    <small class="text-muted text-uppercase">{{ $ticket->prioridad ?? 'normal' }}</small>
                                </td>
                                <td class="text-end">
                                    {{ $ticket->moneda }} {{ number_format((float) $ticket->total, 2) }}
                                </td>
                                <td class="text-end">
                                    @if((float) $ticket->saldo_pendiente > 0)
                                        <span class="badge bg-label-danger">{{ $ticket->moneda }} {{ number_format((float) $ticket->saldo_pendiente, 2) }}</span>
                                    @else
                                        <span class="badge bg-label-success">{{ $ticket->moneda }} 0.00</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center text-muted">
                                        <i class="ti tabler-search icon-48px mb-2"></i>
                                        <h6 class="mb-1">{{ __('No se encontraron tickets') }}</h6>
                                        <p class="mb-0">
                                            @if($this->hasActiveFilters)
                                                {{ __('Intenta ajustar los filtros de busqueda.') }}
                                            @else
                                                {{ __('Aun no hay tickets registrados.') }}
                                            @endif
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($tickets->hasPages())
                <div class="card-footer d-flex flex-wrap justify-content-between align-items-center gap-2 mt-4">
                    <small class="text-muted">
                        {{ __('Mostrando') }}
                        {{ $tickets->firstItem() ?? 0 }}
                        -
                        {{ $tickets->lastItem() ?? 0 }}
                        {{ __('de') }}
                        {{ $tickets->total() ?? 0 }}
                        {{ __('tickets') }}
                    </small>
                    {{ $tickets->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
