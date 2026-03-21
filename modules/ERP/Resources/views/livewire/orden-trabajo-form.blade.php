<div>
    @php
        $tipoOrdenMeta = $tipoOrdenOptions;
        $estadoOtMeta = $estadoOtOptions;
        $prioridadMeta = $prioridadOptions;
        $tipoDetalleMeta = $tipoDetalleOptions;
        $tipoVisionMeta = $tipoVisionOptions;
    @endphp

    <form wire:submit="guardar">
        <div class="card-body">
            @if(session()->has('status'))
                <div class="alert alert-success alert-dismissible mb-4" role="alert">
                    <i class="ti tabler-check me-2"></i>{{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger mb-4">
                    <div class="fw-semibold mb-1">{{ __('Hay errores de validacion.') }}</div>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card border shadow-none mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Cabecera de la OT') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-3">
                            <label class="form-label">{{ __('Numero OT sugerido') }}</label>
                            <input type="text" class="form-control" value="{{ $numeroOtPreview }}" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('Fecha de orden') }}</label>
                            <input type="datetime-local" class="form-control @error('fecha_orden') is-invalid @enderror" wire:model.defer="fecha_orden">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('Fecha prometida') }}</label>
                            <input type="datetime-local" class="form-control @error('fecha_prometida') is-invalid @enderror" wire:model.defer="fecha_prometida">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('Tipo de orden') }}</label>
                            <select class="form-select @error('tipo_orden') is-invalid @enderror" wire:model.live="tipo_orden">
                                @foreach($tipoOrdenMeta as $value => $label)
                                    <option value="{{ $value }}">{{ __($label) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">{{ __('Estado inicial') }}</label>
                            <select class="form-select @error('estado_ot') is-invalid @enderror" wire:model.live="estado_ot">
                                @foreach($estadoOtMeta as $value => $label)
                                    <option value="{{ $value }}">{{ __($label) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('Prioridad') }}</label>
                            <select class="form-select @error('prioridad') is-invalid @enderror" wire:model.live="prioridad">
                                @foreach($prioridadMeta as $value => $label)
                                    <option value="{{ $value }}">{{ __($label) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('Ticket relacionado') }}</label>
                            <select class="form-select @error('ticket_id') is-invalid @enderror" wire:model.live="ticket_id">
                                <option value="">{{ __('Sin ticket') }}</option>
                                @foreach($ticketOptions as $ticket)
                                    <option value="{{ $ticket['id'] }}">{{ $ticket['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('Receta relacionada') }}</label>
                            <select class="form-select @error('receta_id') is-invalid @enderror" wire:model.live="receta_id">
                                <option value="">{{ __('Sin receta') }}</option>
                                @foreach($recetaOptions as $receta)
                                    <option value="{{ $receta['id'] }}">{{ $receta['label'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('Paciente') }}</label>
                            <select class="form-select @error('paciente_id') is-invalid @enderror" wire:model.live="paciente_id" @disabled($pacienteBloqueado)>
                                <option value="">{{ __('Selecciona paciente') }}</option>
                                @foreach($patientOptions as $patient)
                                    <option value="{{ $patient['id'] }}">{{ $patient['label'] }}</option>
                                @endforeach
                            </select>
                            @if($pacienteBloqueado)
                                <small class="text-muted">{{ __('El paciente se autocompleto desde ticket o receta.') }}</small>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Indicaciones de entrega') }}</label>
                            <textarea class="form-control @error('indicaciones_entrega') is-invalid @enderror" rows="2" wire:model.defer="indicaciones_entrega"></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label">{{ __('Observaciones generales') }}</label>
                            <textarea class="form-control @error('observaciones') is-invalid @enderror" rows="3" wire:model.defer="observaciones"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="mb-1">{{ __('Items de la orden') }}</h5>
                    <small class="text-muted">{{ __('Cada item puede tener su propio detalle tecnico de lentes.') }}</small>
                </div>
                <button type="button" class="btn btn-outline-primary" wire:click="agregarItem">
                    <i class="ti tabler-plus me-1"></i>{{ __('Agregar item') }}
                </button>
            </div>

            @foreach($items as $index => $item)
                @php
                    $subtotalActual = null;
                    if (($item['precio_unitario'] ?? '') !== '') {
                        $subtotalActual = (float) ($item['cantidad'] ?? 0) * (float) ($item['precio_unitario'] ?? 0);
                    }
                @endphp
                <div class="card border shadow-none mb-4" wire:key="ot-item-{{ $index }}">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ __('Item') }} {{ $index + 1 }}</h5>
                            <small class="text-muted">{{ __('Detalle comercial y tecnico del item.') }}</small>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-info" wire:click="alternarDetalleLente({{ $index }})">
                                <i class="ti tabler-glasses me-1"></i>
                                {{ !empty($item['requiere_lente']) ? __('Ocultar lente') : __('Detalle de lente') }}
                            </button>
                            @if(count($items) > 1)
                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="quitarItem({{ $index }})">
                                    <i class="ti tabler-trash me-1"></i>{{ __('Quitar') }}
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-2">
                                <label class="form-label">{{ __('Tipo') }}</label>
                                <select class="form-select @error("items.$index.tipo_detalle") is-invalid @enderror" wire:model.live="items.{{ $index }}.tipo_detalle">
                                    @foreach($tipoDetalleMeta as $value => $label)
                                        <option value="{{ $value }}">{{ __($label) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ __('Catalogo relacionado') }}</label>
                                <select class="form-select @error("items.$index.catalogo_id") is-invalid @enderror" wire:model.defer="items.{{ $index }}.catalogo_id">
                                    <option value="">{{ __('Sin catalogo') }}</option>
                                    @foreach($catalogoOptions as $catalogo)
                                        <option value="{{ $catalogo['id'] }}">{{ $catalogo['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Descripcion') }}</label>
                                <input type="text" class="form-control @error("items.$index.descripcion") is-invalid @enderror" wire:model.defer="items.{{ $index }}.descripcion" placeholder="{{ __('Ej. Lunales progresivos con antireflejo') }}">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">{{ __('Cantidad') }}</label>
                                <input type="number" step="0.01" class="form-control @error("items.$index.cantidad") is-invalid @enderror" wire:model.live="items.{{ $index }}.cantidad">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">{{ __('Unidad') }}</label>
                                <input type="text" class="form-control @error("items.$index.unidad") is-invalid @enderror" wire:model.defer="items.{{ $index }}.unidad">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">{{ __('Precio unitario') }}</label>
                                <input type="number" step="0.01" class="form-control @error("items.$index.precio_unitario") is-invalid @enderror" wire:model.live="items.{{ $index }}.precio_unitario">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">{{ __('Subtotal') }}</label>
                                <input type="text" class="form-control" value="{{ $subtotalActual !== null ? number_format($subtotalActual, 2, '.', '') : '' }}" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ __('Observaciones del item') }}</label>
                                <input type="text" class="form-control @error("items.$index.observaciones") is-invalid @enderror" wire:model.defer="items.{{ $index }}.observaciones">
                            </div>

                            @if(!empty($item['requiere_lente']))
                                <div class="col-12">
                                    <div class="border rounded p-3 bg-lighter">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0">{{ __('Detalle tecnico de lente') }}</h6>
                                            <span class="badge bg-label-info">{{ __('Opcional pero recomendado para produccion') }}</span>
                                        </div>

                                        <div class="row g-4">
                                            <div class="col-md-3">
                                                <label class="form-label">{{ __('Tipo de vision') }}</label>
                                                <select class="form-select" wire:model.defer="items.{{ $index }}.lente.tipo_vision">
                                                    <option value="">{{ __('Selecciona') }}</option>
                                                    @foreach($tipoVisionMeta as $value => $label)
                                                        <option value="{{ $value }}">{{ __($label) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">{{ __('Material ID') }}</label>
                                                <input type="number" class="form-control" wire:model.defer="items.{{ $index }}.lente.material_id">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">{{ __('Tratamiento ID') }}</label>
                                                <input type="number" class="form-control" wire:model.defer="items.{{ $index }}.lente.tratamiento_id">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">{{ __('Indice ID') }}</label>
                                                <input type="number" class="form-control" wire:model.defer="items.{{ $index }}.lente.indice_id">
                                            </div>

                                            <div class="col-md-6">
                                                <div class="border rounded p-3 h-100">
                                                    <h6 class="mb-3">{{ __('Ojo derecho') }}</h6>
                                                    <div class="row g-3">
                                                        <div class="col-6"><label class="form-label">{{ __('Esferico') }}</label><input type="number" step="0.01" class="form-control" wire:model.defer="items.{{ $index }}.lente.od_esferico"></div>
                                                        <div class="col-6"><label class="form-label">{{ __('Cilindro') }}</label><input type="number" step="0.01" class="form-control" wire:model.defer="items.{{ $index }}.lente.od_cilindro"></div>
                                                        <div class="col-6"><label class="form-label">{{ __('Eje') }}</label><input type="number" class="form-control" wire:model.defer="items.{{ $index }}.lente.od_eje"></div>
                                                        <div class="col-6"><label class="form-label">{{ __('Adicion') }}</label><input type="number" step="0.01" class="form-control" wire:model.defer="items.{{ $index }}.lente.od_adicion"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="border rounded p-3 h-100">
                                                    <h6 class="mb-3">{{ __('Ojo izquierdo') }}</h6>
                                                    <div class="row g-3">
                                                        <div class="col-6"><label class="form-label">{{ __('Esferico') }}</label><input type="number" step="0.01" class="form-control" wire:model.defer="items.{{ $index }}.lente.oi_esferico"></div>
                                                        <div class="col-6"><label class="form-label">{{ __('Cilindro') }}</label><input type="number" step="0.01" class="form-control" wire:model.defer="items.{{ $index }}.lente.oi_cilindro"></div>
                                                        <div class="col-6"><label class="form-label">{{ __('Eje') }}</label><input type="number" class="form-control" wire:model.defer="items.{{ $index }}.lente.oi_eje"></div>
                                                        <div class="col-6"><label class="form-label">{{ __('Adicion') }}</label><input type="number" step="0.01" class="form-control" wire:model.defer="items.{{ $index }}.lente.oi_adicion"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label">{{ __('DP') }}</label>
                                                <input type="number" step="0.01" class="form-control" wire:model.defer="items.{{ $index }}.lente.dp">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">{{ __('Altura oblea') }}</label>
                                                <input type="number" step="0.01" class="form-control" wire:model.defer="items.{{ $index }}.lente.altura_oblea">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">{{ __('Observaciones tecnicas') }}</label>
                                                <input type="text" class="form-control" wire:model.defer="items.{{ $index }}.lente.observaciones_tecnicas">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ group_route('erp.work-orders.index') }}" class="btn btn-outline-secondary">
                <i class="ti tabler-x me-1"></i>{{ __('Cancelar') }}
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="ti tabler-device-floppy me-1"></i>{{ __('Registrar OT') }}
            </button>
        </div>
    </form>
</div>
