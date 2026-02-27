<div>
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title d-flex align-items-center gap-2">
                        <i class="ti tabler-adjustments-horizontal"></i>
                        <span>{{ __('Combinaciones de medida') }}</span>
                    </h5>
                    <button type="button" class="btn-close border" wire:click="cerrarModal"
                        aria-label="{{ __('Cerrar') }}"></button>
                </div>
                <div class="modal-body">
                    <div class="card shadow-none border mb-4">
                        <div class="card-header py-2">
                            <button type="button"
                                class="btn btn-sm btn-text-secondary p-0 w-100 d-flex align-items-center justify-content-between"
                                data-bs-toggle="collapse" data-bs-target="#catalogoMedidasRegistroCollapse"
                                aria-expanded="true" aria-controls="catalogoMedidasRegistroCollapse">
                                <span class="fw-semibold">
                                    <i class="ti tabler-adjustments me-1"></i>{{ __('Registro de combinaciones') }}
                                </span>
                                <i class="ti tabler-chevron-down"></i>
                            </button>
                        </div>
                        <div class="collapse show" id="catalogoMedidasRegistroCollapse">
                            <div class="card-body">
                                <div class="alert alert-info mb-3 py-2">
                                    <div class="d-flex flex-wrap gap-3 small">
                                        <div><strong>{{ __('Registro') }}:</strong> <span>{{ $catalogoId > 0 ? $catalogoId : '-' }}</span></div>
                                        <div><strong>{{ __('Categoria') }}:</strong> <span>{{ $categoriaNombre !== '' ? $categoriaNombre : '-' }}</span></div>
                                        <div><strong>{{ __('Codigo categoria') }}:</strong> <span>{{ $categoriaCodigo !== '' ? $categoriaCodigo : '-' }}</span></div>
                                        <div><strong>{{ __('Articulo') }}:</strong> <span>{{ $articuloCodigo !== '' ? $articuloCodigo : '-' }}</span></div>
                                        <div><strong>{{ __('Subcategoria') }}:</strong> <span>{{ $articuloSubcategoria !== '' ? $articuloSubcategoria : '-' }}</span></div>
                                    </div>
                                    <div class="mt-1 small">
                                        <strong>{{ __('Descripcion') }}:</strong>
                                        <span>{{ $articuloDescripcion !== '' ? $articuloDescripcion : '-' }}</span>
                                    </div>
                                </div>

                                <form wire:submit="guardarCombinacion">
                                    <div class="row g-3">
                                        <div class="col-lg-7">
                                            <div class="card shadow-none border h-100">
                                                <div class="card-header py-2 bg-label-primary">
                                                    <strong>{{ __('Combinacion de caracteristicas') }}</strong>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row g-2 mt-3 mb-4">
                                                        <div class="col-md-6">
                                                            <label class="form-label mb-1">{{ __('Serie Visual') }}</label>
                                                            <select class="form-select form-select-sm" wire:model.live="serieVisualId">
                                                                <option value="">{{ __('Seleccione') }}</option>
                                                                @foreach ($this->seriesVisuales as $option)
                                                                    <option value="{{ (int) ($option['id'] ?? 0) }}">
                                                                        {{ $this->etiquetaOpcion((array) $option) }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('serieVisualId')
                                                                <small class="text-danger">{{ $message }}</small>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label mb-1">{{ __('Subserie Visual') }}</label>
                                                            <select class="form-select form-select-sm" wire:model.live="subserieVisualId">
                                                                <option value="">{{ __('Seleccione') }}</option>
                                                                @foreach ($this->subseriesVisuales as $option)
                                                                    <option value="{{ (int) ($option['id'] ?? 0) }}">
                                                                        {{ $this->etiquetaOpcion((array) $option) }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('subserieVisualId')
                                                                <small class="text-danger">{{ $message }}</small>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="card shadow-none border mb-3">
                                                        <div class="card-header py-2 bg-label-primary">
                                                            <strong>{{ __('Intervalo de medidas') }}</strong>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row g-2 my-1">
                                                                <div class="col-4"></div>
                                                                <div class="col-4 text-uppercase fw-semibold small">{{ __('Desde') }}</div>
                                                                <div class="col-4 text-uppercase fw-semibold small">{{ __('Hasta') }}</div>
                                                            </div>
                                                            <div class="row g-2 align-items-start mb-2">
                                                                <div class="col-4">{{ __('Medida esferica') }}</div>
                                                                <div class="col-4">
                                                                    <select class="form-select form-select-sm" wire:model.live="medidaEsfericaDesdeId">
                                                                        <option value="">{{ __('Seleccione') }}</option>
                                                                        @foreach ($this->medidasEsfericas as $option)
                                                                            <option value="{{ (int) ($option['id'] ?? 0) }}">
                                                                                {{ $this->etiquetaOpcion((array) $option) }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                    @error('medidaEsfericaDesdeId')
                                                                        <small class="text-danger">{{ $message }}</small>
                                                                    @enderror
                                                                </div>
                                                                <div class="col-4">
                                                                    <select class="form-select form-select-sm" wire:model.live="medidaEsfericaHastaId">
                                                                        <option value="">{{ __('Seleccione') }}</option>
                                                                        @foreach ($this->medidasEsfericas as $option)
                                                                            <option value="{{ (int) ($option['id'] ?? 0) }}">
                                                                                {{ $this->etiquetaOpcion((array) $option) }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                    @error('medidaEsfericaHastaId')
                                                                        <small class="text-danger">{{ $message }}</small>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="row g-2 align-items-start">
                                                                <div class="col-4">{{ __('Medida cilindrica') }}</div>
                                                                <div class="col-4">
                                                                    <select class="form-select form-select-sm" wire:model.live="medidaCilindricaDesdeId">
                                                                        <option value="">{{ __('Seleccione') }}</option>
                                                                        @foreach ($this->medidasCilindricas as $option)
                                                                            <option value="{{ (int) ($option['id'] ?? 0) }}">
                                                                                {{ $this->etiquetaOpcion((array) $option) }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                    @error('medidaCilindricaDesdeId')
                                                                        <small class="text-danger">{{ $message }}</small>
                                                                    @enderror
                                                                </div>
                                                                <div class="col-4">
                                                                    <select class="form-select form-select-sm" wire:model.live="medidaCilindricaHastaId">
                                                                        <option value="">{{ __('Seleccione') }}</option>
                                                                        @foreach ($this->medidasCilindricas as $option)
                                                                            <option value="{{ (int) ($option['id'] ?? 0) }}">
                                                                                {{ $this->etiquetaOpcion((array) $option) }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                    @error('medidaCilindricaHastaId')
                                                                        <small class="text-danger">{{ $message }}</small>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="card shadow-none border">
                                                        <div class="card-header py-2 bg-label-primary">
                                                            <strong>{{ __('Intervalo de adicion') }}</strong>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row g-2 my-1">
                                                                <div class="col-4"></div>
                                                                <div class="col-4 text-uppercase fw-semibold small">{{ __('Desde') }}</div>
                                                                <div class="col-4 text-uppercase fw-semibold small">{{ __('Hasta') }}</div>
                                                            </div>
                                                            <div class="row g-2 align-items-start">
                                                                <div class="col-4">{{ __('Adicion') }}</div>
                                                                <div class="col-4">
                                                                    <select class="form-select form-select-sm" wire:model.live="adicionDesdeId">
                                                                        <option value="">{{ __('Seleccione') }}</option>
                                                                        @foreach ($this->adicionesDisponibles as $option)
                                                                            <option value="{{ (int) ($option['id'] ?? 0) }}">
                                                                                {{ $this->etiquetaOpcion((array) $option) }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                    @error('adicionDesdeId')
                                                                        <small class="text-danger">{{ $message }}</small>
                                                                    @enderror
                                                                </div>
                                                                <div class="col-4">
                                                                    <select class="form-select form-select-sm" wire:model.live="adicionHastaId">
                                                                        <option value="">{{ __('Seleccione') }}</option>
                                                                        @foreach ($this->adicionesDisponibles as $option)
                                                                            <option value="{{ (int) ($option['id'] ?? 0) }}">
                                                                                {{ $this->etiquetaOpcion((array) $option) }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                    @error('adicionHastaId')
                                                                        <small class="text-danger">{{ $message }}</small>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-5">
                                            <div class="card shadow-none border h-100">
                                                <div class="card-header py-2 bg-label-primary">
                                                    <strong>{{ __('Precio de combinacion de caracteristica') }}</strong>
                                                </div>
                                                <div class="card-body">
                                                    <div class="card shadow-none border my-3">
                                                        <div class="card-header py-2 bg-label-primary">
                                                            <strong>{{ __('Precio normal') }}</strong>
                                                        </div>
                                                        <div class="card-body pt-3">
                                                            <label class="form-label mb-1">{{ __('Precio costo') }}</label>
                                                            <input type="text" class="form-control form-control-sm" wire:model.blur="precioBase"
                                                                placeholder="0.00">
                                                            @error('precioBase')
                                                                <small class="text-danger">{{ $message }}</small>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="card shadow-none border mb-3">
                                                        <div class="card-header py-2 bg-label-primary">
                                                            <strong>{{ __('Precio por menor') }}</strong>
                                                        </div>
                                                        <div class="card-body pt-3">
                                                            <div class="row g-2">
                                                                <div class="col-md-4">
                                                                    <label class="form-label mb-1">{{ __('Minimo') }}</label>
                                                                    <input type="text" class="form-control form-control-sm"
                                                                        wire:model.blur="precioXMenorMinimo" placeholder="0.00">
                                                                    @error('precioXMenorMinimo')
                                                                        <small class="text-danger">{{ $message }}</small>
                                                                    @enderror
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label mb-1">{{ __('Base') }}</label>
                                                                    <input type="text" class="form-control form-control-sm"
                                                                        wire:model.blur="precioXMenorBase" placeholder="0.00">
                                                                    @error('precioXMenorBase')
                                                                        <small class="text-danger">{{ $message }}</small>
                                                                    @enderror
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label mb-1">{{ __('Maximo') }}</label>
                                                                    <input type="text" class="form-control form-control-sm"
                                                                        wire:model.blur="precioXMenorMaximo" placeholder="0.00">
                                                                    @error('precioXMenorMaximo')
                                                                        <small class="text-danger">{{ $message }}</small>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="card shadow-none border">
                                                        <div class="card-header py-2 bg-label-primary">
                                                            <strong>{{ __('Precio por mayor') }}</strong>
                                                        </div>
                                                        <div class="card-body pt-3">
                                                            <div class="row g-2">
                                                                <div class="col-md-4">
                                                                    <label class="form-label mb-1">{{ __('Minimo') }}</label>
                                                                    <input type="text" class="form-control form-control-sm"
                                                                        wire:model.blur="precioXMayorMinimo" placeholder="0.00">
                                                                    @error('precioXMayorMinimo')
                                                                        <small class="text-danger">{{ $message }}</small>
                                                                    @enderror
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label mb-1">{{ __('Base') }}</label>
                                                                    <input type="text" class="form-control form-control-sm"
                                                                        wire:model.blur="precioXMayorBase" placeholder="0.00">
                                                                    @error('precioXMayorBase')
                                                                        <small class="text-danger">{{ $message }}</small>
                                                                    @enderror
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label mb-1">{{ __('Maximo') }}</label>
                                                                    <input type="text" class="form-control form-control-sm"
                                                                        wire:model.blur="precioXMayorMaximo" placeholder="0.00">
                                                                    @error('precioXMayorMaximo')
                                                                        <small class="text-danger">{{ $message }}</small>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end gap-2 mt-3">
                                        <button type="button" class="btn btn-label-secondary" wire:click="limpiarFormulario">
                                            <i class="ti tabler-eraser me-1"></i>{{ __('Limpiar') }}
                                        </button>
                                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled"
                                            wire:target="guardarCombinacion">
                                            <span wire:loading.remove wire:target="guardarCombinacion">
                                                <i class="ti tabler-device-floppy me-1"></i>{{ __('Registrar') }}
                                            </span>
                                            <span wire:loading wire:target="guardarCombinacion">
                                                <span class="spinner-border spinner-border-sm me-1"></span>
                                                {{ __('Guardando...') }}
                                            </span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-none border">
                        <div class="card-body">
                            @php($combinaciones = $this->combinaciones)
                            <div class="table-responsive">
                                <table class="table table-sm table-hover text-center align-middle mb-2">
                                    <thead class="text-center">
                                        <tr>
                                            <th style="width: 8rem;">{{ __('Acciones') }}</th>
                                            <th style="width: 8rem;">{{ __('Estado') }}</th>
                                            <th>{{ __('Serie Visual') }}</th>
                                            <th>{{ __('Subserie Visual') }}</th>
                                            <th>{{ __('Medida Esferica') }}</th>
                                            <th>{{ __('Medida Cilindrica') }}</th>
                                            <th>{{ __('Adicion') }}</th>
                                            <th style="width: 10rem;">{{ __('Precio base') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($combinaciones as $row)
                                            <tr>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-label-danger"
                                                        wire:click="eliminarCombinacion({{ $row['id'] }})"
                                                        onclick="return confirm('{{ __('Desea eliminar este registro?') }}')">
                                                        <i class="ti tabler-trash"></i>
                                                    </button>
                                                </td>
                                                <td>
                                                    <span class="badge {{ (int) $row['estado'] === 1 ? 'bg-label-success' : 'bg-label-secondary' }}">
                                                        {{ (int) $row['estado'] === 1 ? __('Activo') : __('Inactivo') }}
                                                    </span>
                                                </td>
                                                <td>{{ $row['serie_visual'] }}</td>
                                                <td>{{ $row['subserie_visual'] }}</td>
                                                <td>{{ $row['medida_esferica_desde'] }} | {{ $row['medida_esferica_hasta'] }}</td>
                                                <td>{{ $row['medida_cilindrica_desde'] }} | {{ $row['medida_cilindrica_hasta'] }}</td>
                                                <td>{{ $row['adicion_desde'] }} | {{ $row['adicion_hasta'] }}</td>
                                                <td>{{ $row['preciobase'] }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-4">
                                                    {{ __('Ningun dato disponible en esta tabla') }}
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 small text-muted">
                                <div>{{ __('Mostrando') }} {{ count($combinaciones) }} {{ __('registros') }}</div>
                                <div>{{ __('Total') }}: {{ count($combinaciones) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" wire:click="cerrarModal">
                        {{ __('Cerrar') }}
                    </button>
                </div>
                </div>
            </div>
        </div>
    @endif
</div>