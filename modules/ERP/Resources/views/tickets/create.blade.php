@extends('layouts.app')

@section('title', __('Nuevo Ticket'))

@php
    $group = current_group();
    $groupCode = current_group_code() ?? request()->route('group') ?? 'PE';
    $patientOptions = $patientOptions ?? [];
    $ticketNumberPreview = $ticketNumberPreview ?? 'TK-000000';
    $breadcrumbs = [
        'title' => '',
        'description' => '',
        'icon' => 'ti tabler-ticket',
        'items' => [
            ['name' => 'ERP', 'url' => url(app()->getLocale() . '/' . $groupCode . '/erp')],
            ['name' => __('Atencion al Cliente'), 'url' => group_route('erp.tickets.index')],
            ['name' => __('Nuevo Ticket')],
        ],
    ];
@endphp

@section('breadcrumb')
    <x-breadcrumbs :items="$breadcrumbs">
        <x-slot:extra>
            <div class="d-flex align-items-center gap-2">
                @if($group)
                    <span class="badge bg-label-primary" title="{{ $group->business_name ?? $group->trade_name }}">
                        <i class="ti tabler-map-pin me-1"></i>{{ $group->code }}
                    </span>
                @endif
                <span class="badge bg-label-success">
                    <i class="ti tabler-plus"></i>
                </span>
            </div>
        </x-slot:extra>
    </x-breadcrumbs>
@endsection

@section('content')

@if($errors->any())
    <div class="alert alert-danger mb-6">
        <i class="ti tabler-alert-circle me-2"></i>
        <strong>{{ __('Revisa los campos del formulario.') }}</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form id="ticket-create-form" action="{{ group_route('erp.tickets.store') }}" method="POST">
    @csrf
    <div class="row g-6">
        <div class="col-lg-8">
            <div class="card mb-6">
                <x-card-header
                    title="{{ __('Datos principales') }}"
                    description="{{ __('Registra la atencion y selecciona al paciente desde Partners.') }}"
                    icon="ti tabler-ticket"
                    iconColor="bg-label-info"
                />
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label">{{ __('Nro ticket') }}</label>
                            <input type="text" class="form-control" value="{{ $ticketNumberPreview }}" readonly>
                            <small class="text-muted">{{ __('Se asigna automaticamente al guardar.') }}</small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('Fecha de ticket') }}</label>
                            <input
                                type="datetime-local"
                                name="fecha_ticket"
                                value="{{ old('fecha_ticket', now()->format('Y-m-d\TH:i')) }}"
                                class="form-control @error('fecha_ticket') is-invalid @enderror"
                                required
                            >
                            @error('fecha_ticket')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('Canal') }}</label>
                            <select name="canal" class="form-select @error('canal') is-invalid @enderror">
                                <option value="" @selected(old('canal') === null)>{{ __('Seleccione') }}</option>
                                <option value="mostrador" @selected(old('canal', 'mostrador') === 'mostrador')>{{ __('Mostrador') }}</option>
                                <option value="cita" @selected(old('canal') === 'cita')>{{ __('Cita') }}</option>
                                <option value="telefono" @selected(old('canal') === 'telefono')>{{ __('Telefono') }}</option>
                                <option value="web" @selected(old('canal') === 'web')>{{ __('Web') }}</option>
                            </select>
                            @error('canal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">{{ __('Paciente') }}</label>
                            <input
                                type="text"
                                id="ticket-create-paciente-label"
                                name="paciente_label"
                                value="{{ old('paciente_label') }}"
                                class="form-control @error('paciente_id') is-invalid @enderror @error('paciente_label') is-invalid @enderror"
                                placeholder="{{ __('Escribe nombre o documento y selecciona') }}"
                                list="tickets-create-pacientes-list"
                                autocomplete="off"
                                required
                            >
                            <input type="hidden" id="ticket-create-paciente-id" name="paciente_id" value="{{ old('paciente_id') }}">
                            <datalist id="tickets-create-pacientes-list">
                                @foreach($patientOptions as $patient)
                                    <option value="{{ $patient['label'] }}" data-id="{{ $patient['id'] }}"></option>
                                @endforeach
                            </datalist>
                            <small class="text-muted">{{ __('Solo se muestran pacientes del contexto activo.') }}</small>
                            @error('paciente_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @error('paciente_label')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">{{ __('Estado') }}</label>
                            <select name="estado_ticket" class="form-select @error('estado_ticket') is-invalid @enderror" required>
                                <option value="abierto" @selected(old('estado_ticket', 'abierto') === 'abierto')>{{ __('Abierto') }}</option>
                                <option value="en_proceso" @selected(old('estado_ticket') === 'en_proceso')>{{ __('En proceso') }}</option>
                                <option value="listo" @selected(old('estado_ticket') === 'listo')>{{ __('Listo') }}</option>
                                <option value="cerrado" @selected(old('estado_ticket') === 'cerrado')>{{ __('Cerrado') }}</option>
                                <option value="anulado" @selected(old('estado_ticket') === 'anulado')>{{ __('Anulado') }}</option>
                            </select>
                            @error('estado_ticket')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">{{ __('Prioridad') }}</label>
                            <select name="prioridad" class="form-select @error('prioridad') is-invalid @enderror" required>
                                <option value="baja" @selected(old('prioridad') === 'baja')>{{ __('Baja') }}</option>
                                <option value="normal" @selected(old('prioridad', 'normal') === 'normal')>{{ __('Normal') }}</option>
                                <option value="alta" @selected(old('prioridad') === 'alta')>{{ __('Alta') }}</option>
                                <option value="urgente" @selected(old('prioridad') === 'urgente')>{{ __('Urgente') }}</option>
                            </select>
                            @error('prioridad')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">{{ __('Resumen') }}</label>
                            <textarea
                                name="resumen"
                                rows="3"
                                class="form-control @error('resumen') is-invalid @enderror"
                                placeholder="{{ __('Motivo de atencion, observaciones iniciales, etc.') }}"
                            >{{ old('resumen') }}</textarea>
                            @error('resumen')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-6">
                <x-card-header
                    title="{{ __('Importes') }}"
                    description="{{ __('Define montos para subtotal, descuento, impuesto y saldo.') }}"
                    icon="ti tabler-currency-dollar"
                    iconColor="bg-label-warning"
                />
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label">{{ __('Moneda') }}</label>
                            <select name="moneda" class="form-select @error('moneda') is-invalid @enderror" required>
                                <option value="PEN" @selected(old('moneda', 'PEN') === 'PEN')>PEN</option>
                                <option value="USD" @selected(old('moneda') === 'USD')>USD</option>
                            </select>
                            @error('moneda')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">{{ __('Subtotal') }}</label>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                name="subtotal"
                                id="ticket-create-subtotal"
                                value="{{ old('subtotal', '0.00') }}"
                                class="form-control @error('subtotal') is-invalid @enderror"
                                required
                            >
                            @error('subtotal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">{{ __('Descuento') }}</label>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                name="descuento_total"
                                id="ticket-create-descuento"
                                value="{{ old('descuento_total', '0.00') }}"
                                class="form-control @error('descuento_total') is-invalid @enderror"
                            >
                            @error('descuento_total')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">{{ __('Impuesto') }}</label>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                name="impuesto_total"
                                id="ticket-create-impuesto"
                                value="{{ old('impuesto_total', '0.00') }}"
                                class="form-control @error('impuesto_total') is-invalid @enderror"
                            >
                            @error('impuesto_total')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">{{ __('Total calculado') }}</label>
                            <input type="text" id="ticket-create-total-preview" class="form-control" value="0.00" readonly>
                        </div>

                        <div class="col-12">
                            <label class="form-label">{{ __('Saldo pendiente') }}</label>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                name="saldo_pendiente"
                                value="{{ old('saldo_pendiente') }}"
                                class="form-control @error('saldo_pendiente') is-invalid @enderror"
                                placeholder="{{ __('Vacio = automatico') }}"
                            >
                            @error('saldo_pendiente')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column gap-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="ti tabler-device-floppy me-1"></i>{{ __('Guardar Ticket') }}
                </button>
                <a href="{{ group_route('erp.tickets.index') }}" class="btn btn-label-secondary w-100">
                    <i class="ti tabler-arrow-left me-1"></i>{{ __('Volver') }}
                </a>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
(() => {
    const form = document.getElementById('ticket-create-form');
    const patientInput = document.getElementById('ticket-create-paciente-label');
    const patientIdInput = document.getElementById('ticket-create-paciente-id');
    const patientList = document.getElementById('tickets-create-pacientes-list');
    const endpoint = @json(group_route('erp.tickets.patients.search'));

    const subtotalInput = document.getElementById('ticket-create-subtotal');
    const discountInput = document.getElementById('ticket-create-descuento');
    const taxInput = document.getElementById('ticket-create-impuesto');
    const totalPreview = document.getElementById('ticket-create-total-preview');

    let patientTimer = null;
    let selectedPatient = {
        id: (patientIdInput?.value || '').trim(),
        label: (patientInput?.value || '').trim(),
    };

    const normalize = (value) => (value || '').trim().toLowerCase();

    const clearSelectedPatient = () => {
        selectedPatient = { id: '', label: '' };
        if (patientIdInput) {
            patientIdInput.value = '';
        }
    };

    const setSelectedPatient = (id, label) => {
        const normalizedId = String(id || '').trim();
        const normalizedLabel = String(label || '').trim();

        selectedPatient = {
            id: normalizedId,
            label: normalizedLabel,
        };

        if (patientIdInput) {
            patientIdInput.value = normalizedId;
        }
    };

    const parseAmount = (value) => {
        const number = Number.parseFloat(value ?? '0');
        return Number.isFinite(number) ? number : 0;
    };

    const recalcTotal = () => {
        const subtotal = parseAmount(subtotalInput?.value);
        const discount = parseAmount(discountInput?.value);
        const tax = parseAmount(taxInput?.value);

        const total = Math.max(subtotal - discount, 0) + tax;
        if (totalPreview) {
            totalPreview.value = total.toFixed(2);
        }
    };

    const findPatientOptionByLabel = (label) => {
        if (!patientList) {
            return null;
        }

        const normalized = normalize(label);
        return Array.from(patientList.options).find((option) => {
            return normalize(option.value) === normalized;
        }) || null;
    };

    const ensureSelectedOption = () => {
        if (!patientList || !selectedPatient.id || !selectedPatient.label) {
            return;
        }

        const exists = Array.from(patientList.options).some((option) => {
            return normalize(option.value) === normalize(selectedPatient.label);
        });

        if (exists) {
            return;
        }

        const option = document.createElement('option');
        option.value = selectedPatient.label;
        option.dataset.id = selectedPatient.id;
        patientList.appendChild(option);
    };

    const syncPatientId = ({ clearWhenNotFound = true } = {}) => {
        if (!patientInput || !patientIdInput) {
            return;
        }

        const currentLabel = patientInput.value.trim();
        const option = findPatientOptionByLabel(patientInput.value);

        if (option && option.dataset.id) {
            setSelectedPatient(option.dataset.id, option.value);
            return;
        }

        if (selectedPatient.id && normalize(currentLabel) === normalize(selectedPatient.label)) {
            patientIdInput.value = selectedPatient.id;
            return;
        }

        if (clearWhenNotFound) {
            clearSelectedPatient();
        }
    };

    const setPatientFieldValidity = () => {
        if (!patientInput || !patientIdInput) {
            return;
        }

        const hasLabel = patientInput.value.trim() !== '';
        const hasPatientId = patientIdInput.value.trim() !== '';
        patientInput.setCustomValidity(hasLabel && !hasPatientId ? 'Selecciona un paciente valido de la lista.' : '');
    };

    const renderPatientOptions = (items) => {
        if (!patientList) {
            return;
        }

        const usedLabels = new Set();
        patientList.innerHTML = '';
        items.forEach((item) => {
            if (!item?.label || !item?.id) {
                return;
            }

            const labelKey = normalize(item.label);
            if (usedLabels.has(labelKey)) {
                return;
            }
            usedLabels.add(labelKey);

            const option = document.createElement('option');
            option.value = item.label;
            option.dataset.id = String(item.id);
            patientList.appendChild(option);
        });

        ensureSelectedOption();
    };

    const fetchPatients = async (term) => {
        const params = new URLSearchParams({ q: term, limit: '15' });
        try {
            const response = await fetch(`${endpoint}?${params.toString()}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                return;
            }

            const payload = await response.json();
            if (!payload?.success || !Array.isArray(payload.data)) {
                return;
            }

            renderPatientOptions(payload.data);
            syncPatientId({ clearWhenNotFound: false });
            setPatientFieldValidity();
        } catch (error) {
            // noop
        }
    };

    if (patientInput) {
        patientInput.addEventListener('input', () => {
            const currentValue = patientInput.value.trim();

            if (selectedPatient.id && normalize(currentValue) !== normalize(selectedPatient.label)) {
                clearSelectedPatient();
            }

            syncPatientId({ clearWhenNotFound: false });
            setPatientFieldValidity();

            const term = patientInput.value.trim();
            if (patientTimer) {
                clearTimeout(patientTimer);
            }

            if (selectedPatient.id && normalize(term) === normalize(selectedPatient.label)) {
                return;
            }

            if (term.length < 2) {
                return;
            }

            patientTimer = setTimeout(() => fetchPatients(term), 250);
        });

        patientInput.addEventListener('change', () => {
            syncPatientId();
            setPatientFieldValidity();
        });
    }

    if (form) {
        form.addEventListener('submit', (event) => {
            syncPatientId();
            setPatientFieldValidity();

            if (!form.checkValidity()) {
                event.preventDefault();
                form.reportValidity();
            }
        });
    }

    [subtotalInput, discountInput, taxInput].forEach((input) => {
        if (input) {
            input.addEventListener('input', recalcTotal);
        }
    });

    ensureSelectedOption();
    syncPatientId({ clearWhenNotFound: false });
    setPatientFieldValidity();
    recalcTotal();
})();
</script>
@endpush
