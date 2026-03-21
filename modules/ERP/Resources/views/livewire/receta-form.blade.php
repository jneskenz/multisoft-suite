<div data-receta-form-component>
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

    <form id="receta-form" wire:submit="guardar">
        <div class="row g-6">
            <div class="col-lg-8">
                <div class="card mb-6">
                    <x-card-header
                        title="{{ $esEdicion ? __('Editar Receta') : __('Datos principales') }}"
                        description="{{ __('Cabecera de la receta y vinculo con paciente, ticket y especialista.') }}"
                        icon="ti tabler-file-certificate"
                        iconColor="bg-label-info"
                    />
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-label">{{ __('Nro receta') }}</label>
                                <input type="text" class="form-control" value="{{ $recipeNumberPreview }}" readonly>
                                <small class="text-muted">
                                    {{ $esEdicion ? __('Numero de receta actual.') : __('Se asigna automaticamente al guardar.') }}
                                </small>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">{{ __('Fecha de receta') }}</label>
                                <input
                                    type="datetime-local"
                                    wire:model.defer="fecha_receta"
                                    class="form-control @error('fecha_receta') is-invalid @enderror"
                                    required
                                >
                                @error('fecha_receta')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">{{ __('Estado') }}</label>
                                <select wire:model.defer="estado_receta" class="form-select @error('estado_receta') is-invalid @enderror" required>
                                    <option value="borrador">{{ __('Borrador') }}</option>
                                    <option value="emitida">{{ __('Emitida') }}</option>
                                    <option value="cerrada">{{ __('Cerrada') }}</option>
                                    <option value="anulada">{{ __('Anulada') }}</option>
                                </select>
                                @error('estado_receta')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6" wire:ignore>
                                <label class="form-label">{{ __('Ticket relacionado') }}</label>
                                <select
                                    id="receta-form-ticket-id"
                                    class="select2 form-select @error('ticket_id') is-invalid @enderror"
                                    data-placeholder="{{ __('Sin ticket') }}"
                                >
                                    <option value="">{{ __('Sin ticket') }}</option>
                                    @foreach($ticketOptions as $ticket)
                                        <option
                                            value="{{ $ticket['id'] }}"
                                            data-patient-id="{{ $ticket['patient_id'] ?? '' }}"
                                            data-patient-label="{{ $ticket['patient_label'] ?? '' }}"
                                            @selected((string) $ticket_id === (string) $ticket['id'])
                                        >{{ $ticket['label'] }}</option>
                                    @endforeach
                                </select>
                                @error('ticket_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ __('Paciente') }}</label>
                                <input
                                    type="text"
                                    id="receta-form-paciente-label"
                                    wire:model.defer="paciente_label"
                                    class="form-control @error('paciente_id') is-invalid @enderror @error('paciente_label') is-invalid @enderror"
                                    placeholder="{{ __('Escribe nombre o DNI y selecciona') }}"
                                    list="receta-form-pacientes-list"
                                    autocomplete="off"
                                    @readonly($pacienteBloqueado)
                                    required
                                >
                                <input type="hidden" id="receta-form-paciente-id" wire:model.defer="paciente_id">
                                <datalist id="receta-form-pacientes-list">
                                    @foreach($patientOptions as $patient)
                                        <option value="{{ $patient['label'] }}" data-id="{{ $patient['id'] }}"></option>
                                    @endforeach
                                </datalist>
                                @error('paciente_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                @error('paciente_label')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ __('Especialista') }}</label>
                                <select wire:model.defer="especialista_id" class="form-select @error('especialista_id') is-invalid @enderror">
                                    <option value="">{{ __('Seleccione') }}</option>
                                    @foreach($specialistOptions as $specialist)
                                        <option value="{{ $specialist['id'] }}">{{ $specialist['label'] }}</option>
                                    @endforeach
                                </select>
                                @error('especialista_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">{{ __('Motivo de consulta') }}</label>
                                <textarea
                                    wire:model.defer="motivo_consulta"
                                    rows="3"
                                    class="form-control @error('motivo_consulta') is-invalid @enderror"
                                    placeholder="{{ __('Motivo principal de la atencion') }}"
                                ></textarea>
                                @error('motivo_consulta')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">{{ __('Observaciones generales') }}</label>
                                <textarea
                                    wire:model.defer="observaciones_generales"
                                    rows="4"
                                    class="form-control @error('observaciones_generales') is-invalid @enderror"
                                    placeholder="{{ __('Observaciones o notas generales') }}"
                                ></textarea>
                                @error('observaciones_generales')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-6">
                    <x-card-header
                        title="{{ __('Acciones') }}"
                        description="{{ $esEdicion ? __('Actualiza la cabecera de la receta.') : __('Registra la cabecera y deja listos los detalles clinicos.') }}"
                        icon="ti tabler-device-floppy"
                        iconColor="bg-label-warning"
                    />
                    <div class="card-body">
                        <div class="d-flex flex-column gap-2">
                            <button type="submit" class="btn btn-primary w-100" wire:loading.attr="disabled">
                                <i class="ti tabler-device-floppy me-1"></i>
                                {{ $esEdicion ? __('Actualizar Receta') : __('Guardar Receta') }}
                            </button>
                            <a href="{{ group_route('erp.recetas.index') }}" class="btn btn-label-secondary w-100">
                                <i class="ti tabler-arrow-left me-1"></i>{{ __('Volver') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @once
        <script>
        (() => {
            const initRecetaForm = () => {
                const root = document.querySelector('[data-receta-form-component][wire\\:id][data-receta-form-ready!="1"]');
                if (!root) {
                    return;
                }

                const componentId = root.getAttribute('wire:id');
                const ticketSelect = root.querySelector('#receta-form-ticket-id');
                const patientInput = root.querySelector('#receta-form-paciente-label');
                const patientIdInput = root.querySelector('#receta-form-paciente-id');
                const patientList = root.querySelector('#receta-form-pacientes-list');
                const endpoint = @json(group_route('erp.recetas.patients.search'));

                if (!ticketSelect || !patientInput || !patientIdInput || !patientList || !window.Livewire) {
                    return;
                }

                const livewire = window.Livewire.find(componentId);
                if (!livewire) {
                    return;
                }

                const normalize = (value) => (value || '').trim().toLowerCase();
                let patientTimer = null;
                let selectedPatient = {
                    id: (patientIdInput.value || '').trim(),
                    label: (patientInput.value || '').trim(),
                };

                const dispatchInput = (element) => {
                    element.dispatchEvent(new Event('input', { bubbles: true }));
                    element.dispatchEvent(new Event('change', { bubbles: true }));
                };

                const clearSelectedPatient = () => {
                    selectedPatient = { id: '', label: '' };
                    patientIdInput.value = '';
                    patientInput.value = '';
                    dispatchInput(patientIdInput);
                    dispatchInput(patientInput);
                };

                const setSelectedPatient = (id, label) => {
                    selectedPatient = {
                        id: String(id || '').trim(),
                        label: String(label || '').trim(),
                    };

                    patientIdInput.value = selectedPatient.id;
                    patientInput.value = selectedPatient.label;
                    dispatchInput(patientIdInput);
                    dispatchInput(patientInput);
                };

                const findPatientOptionByLabel = (label) => {
                    return Array.from(patientList.options).find((option) => normalize(option.value) === normalize(label)) || null;
                };

                const ensureSelectedOption = () => {
                    if (!selectedPatient.id || !selectedPatient.label) return;

                    const exists = Array.from(patientList.options).some((option) => normalize(option.value) === normalize(selectedPatient.label));
                    if (exists) return;

                    const option = document.createElement('option');
                    option.value = selectedPatient.label;
                    option.dataset.id = selectedPatient.id;
                    patientList.appendChild(option);
                };

                const syncPatientId = ({ clearWhenNotFound = true } = {}) => {
                    const currentLabel = patientInput.value.trim();
                    const option = findPatientOptionByLabel(patientInput.value);

                    if (option && option.dataset.id) {
                        setSelectedPatient(option.dataset.id, option.value);
                        return;
                    }

                    if (selectedPatient.id && normalize(currentLabel) === normalize(selectedPatient.label)) {
                        patientIdInput.value = selectedPatient.id;
                        dispatchInput(patientIdInput);
                        return;
                    }

                    if (clearWhenNotFound) {
                        selectedPatient = { id: '', label: '' };
                        patientIdInput.value = '';
                        dispatchInput(patientIdInput);
                    }
                };

                const renderPatientOptions = (items) => {
                    const usedLabels = new Set();
                    patientList.innerHTML = '';

                    items.forEach((item) => {
                        if (!item?.label || !item?.id) return;

                        const key = normalize(item.label);
                        if (usedLabels.has(key)) return;

                        usedLabels.add(key);

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

                        if (!response.ok) return;

                        const payload = await response.json();
                        if (!payload?.success || !Array.isArray(payload.data)) return;

                        renderPatientOptions(payload.data);
                        syncPatientId({ clearWhenNotFound: false });
                    } catch (error) {
                    }
                };

                if (window.jQuery && typeof window.jQuery.fn.select2 === 'function') {
                    const ticketSelectJq = window.jQuery(ticketSelect);
                    if (!ticketSelect.parentElement?.classList.contains('position-relative')) {
                        ticketSelectJq.wrap('<div class="position-relative"></div>');
                    }

                    ticketSelectJq.select2({
                        placeholder: ticketSelect.dataset.placeholder || 'Sin ticket',
                        dropdownParent: ticketSelectJq.parent()
                    });

                    ticketSelectJq.on('change select2:select select2:clear', () => {
                        const value = ticketSelectJq.val();
                        livewire.set('ticket_id', value === '' ? null : value);
                    });
                } else {
                    ticketSelect.addEventListener('change', () => {
                        livewire.set('ticket_id', ticketSelect.value === '' ? null : ticketSelect.value);
                    });
                }

                patientInput.addEventListener('input', () => {
                    if (patientInput.readOnly) return;

                    const currentValue = patientInput.value.trim();
                    if (selectedPatient.id && normalize(currentValue) !== normalize(selectedPatient.label)) {
                        selectedPatient = { id: '', label: '' };
                        patientIdInput.value = '';
                        dispatchInput(patientIdInput);
                    }

                    syncPatientId({ clearWhenNotFound: false });

                    const term = patientInput.value.trim();
                    if (patientTimer) clearTimeout(patientTimer);
                    if (selectedPatient.id && normalize(term) === normalize(selectedPatient.label)) return;
                    if (term.length < 2) return;

                    patientTimer = setTimeout(() => fetchPatients(term), 250);
                });

                patientInput.addEventListener('change', () => {
                    if (patientInput.readOnly) return;
                    syncPatientId();
                });

                root.dataset.recetaFormReady = '1';
            };

            const bootRecetaForm = () => {
                initRecetaForm();

                if (window.Livewire && typeof window.Livewire.hook === 'function') {
                    window.Livewire.hook('morph.updated', () => {
                        initRecetaForm();
                    });
                }
            };

            if (window.Livewire) {
                bootRecetaForm();
            } else {
                document.addEventListener('livewire:init', bootRecetaForm, { once: true });
            }
        })();
        </script>
    @endonce
</div>
