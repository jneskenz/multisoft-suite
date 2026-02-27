<div>
    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <form wire:submit="save" class="modal-content" enctype="multipart/form-data">
                    <div class="modal-header">
                        <div class="d-flex w-100 align-items-center justify-content-between gap-2">
                            <h5 class="modal-title">{{ __('Nuevo Catálogo') }}</h5>
                            <h5 class="modal-title">
                                {{ $categoriaNombre !== '' ? 'CATEGORIA ' . $categoriaNombre : __('Categoría') }}
                            </h5>
                            <div></div>
                            <button type="button" class="btn-close border" wire:click="closeModal"
                                aria-label="{{ __('Cerrar') }}"></button>
                        </div>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" wire:model="registroId" name="id">
                        <input type="hidden" wire:model="categoriaId" name="categoria">
                        <input type="hidden" wire:model="table" name="table">
                        <input type="hidden" wire:model="estado" name="estado">

                        <div class="row">
                            <div class="col col-md-10">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label for="catalogoSubcategoria"
                                            class="form-label">{{ __('Subcategoría') }}</label>
                                        <div class="input-group">
                                            <select id="catalogoSubcategoria" wire:model.live="subcategoria"
                                                name="subcategoria" class="form-select">
                                                <option value="" data-codigo="">{{ __('Seleccione') }}</option>
                                                @foreach ($this->fieldOptions('subcategoria') as $option)
                                                    <option value="{{ $option['id'] }}"
                                                        data-codigo="{{ $option['codigo'] ?? '' }}">
                                                        {{ $option['nombre'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="button" class="btn btn-outline-primary px-2"
                                                wire:click="addCatalogOption('subcategoria')"
                                                title="{{ __('Agregar subcategoría') }}">
                                                <i class="ti tabler-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="catalogoCodigo" class="form-label">{{ __('Código') }}</label>
                                        <input type="text" id="catalogoCodigo" wire:model.live="codigo"
                                            name="codigo" class="form-control">
                                    </div>
                                    <div class="col-md-7">
                                        <label for="catalogoDescripcion"
                                            class="form-label">{{ __('Descripción') }}</label>
                                        <div class="input-group input-group-merge">
                                            <span class="input-group-text codigo-descripcion"><span
                                                    class="ti tabler-id-badge-2"></span></span>
                                            <input type="text" id="catalogoDescripcion" wire:model.live="descripcion"
                                                class="form-control" name="descripcion" placeholder="0000001"
                                                aria-label="Descripción del catálogo">
                                        </div>
                                    </div>
                                </div>

                                <div class="divider text-start">
                                    <div class="divider-text">{{ __('Caracteristicas') }}</div>
                                </div>

                                <div class="row g-3">
                                    @forelse ($this->dynamicFields as $campo)
                                        <div class="{{ $campo === 'imagen' ? 'col-md-6' : 'col-md-3' }}"
                                            wire:key="catalogo-campo-{{ $categoriaId }}-{{ $campo }}">
                                            <label class="form-label"
                                                for="caracteristica_{{ $campo }}">{{ $this->fieldLabel($campo) }}</label>
                                            @if ($campo === 'imagen')
                                                <input type="file" class="form-control"
                                                    id="caracteristica_{{ $campo }}"
                                                    wire:model.live="imagenUpload" accept="image/*"
                                                    name="{{ $campo }}">
                                                <div wire:loading wire:target="imagenUpload"
                                                    class="small text-muted mt-1">
                                                    {{ __('Cargando imagen...') }}
                                                </div>
                                                {{-- <small class="text-muted d-block mt-1">
                                                    {{ __('Si no se sube imagen, se mostrará la imagen por defecto.') }}
                                                </small> --}}
                                            @else
                                                <div class="input-group">
                                                    <select class="form-select" id="caracteristica_{{ $campo }}"
                                                        wire:model.live="values.{{ $campo }}"
                                                        @if ($this->isFieldDisabled($campo)) disabled @endif
                                                        name="{{ $campo }}">
                                                        <option value="" data-codigo="">
                                                            {{ $this->isFieldDisabled($campo) ? __('Seleccione color primero') : __('Seleccione') }}
                                                        </option>
                                                        @foreach ($this->fieldOptions($campo) as $option)
                                                            <option value="{{ $option['id'] }}"
                                                                data-codigo="{{ $option['codigo'] ?? '' }}">
                                                                {{ $option['nombre'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <button type="button" class="btn btn-outline-primary px-2"
                                                        wire:click="addCatalogOption('{{ $campo }}')"
                                                        title="{{ __('Agregar ') . $this->fieldLabel($campo) }}">
                                                        <i class="ti tabler-plus"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    @empty
                                        <div class="col-12">
                                            <div class="alert alert-warning mb-0">
                                                {{ __('No hay caracteristicas configuradas para esta categoría.') }}
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                            <div class="col col-md-2 mb-2 border-star">
                                <div class="row">
                                    <div class="col">
                                        <ul class="list-group">
                                            <li class="list-group-item px-3 py-1">Código de barra</li>
                                            <li class="list-group-item p-3 text-center">
                                                <img class="barcode" height="auto" width="100%"
                                                    src="{{ $this->barcodeUrl }}" />
                                            </li>
                                            <li class="list-group-item px-3 py-1">Imagen</li>
                                            <li class="list-group-item p-3">
                                                <img class="img-fluid rounded imgcatalogo"
                                                    src="{{ $this->previewImageUrl }}" height="auto" width="100%"
                                                    alt="Sin imagen">
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary"
                            wire:click="closeModal">{{ __('Cancelar') }}</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="save">{{ __('Guardar') }}</span>
                            <span wire:loading wire:target="save">
                                <span class="spinner-border spinner-border-sm me-1"></span>
                                {{ __('Guardando...') }}
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if ($showFieldCrudModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.60); z-index: 1080;">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="d-flex w-100 align-items-center justify-content-between">
                            <h5 class="modal-title">
                                {{ __('Gestionar') }} {{ $this->fieldLabel($crudField) }}
                            </h5>
                            <button type="button" class="btn-close border" wire:click="closeFieldCrudModal"
                                aria-label="{{ __('Cerrar') }}"></button>
                        </div>
                    </div>

                    <div class="modal-body">
                        @if ($crudRelationColumn)
                            <div class="alert {{ $this->isCrudRelationReady ? 'alert-info' : 'alert-warning' }} py-2">
                                <strong>{{ $crudRelationLabel }}:</strong>
                                @if ($this->isCrudRelationReady)
                                    {{ $crudRelationName !== '' ? $crudRelationName : $crudRelationValue }}
                                @else
                                    {{ $crudRelationMessage !== '' ? $crudRelationMessage : __('Seleccione primero el campo relacionado.') }}
                                @endif
                            </div>
                        @endif

                        <form wire:submit="saveFieldCrudRecord" class="mb-3">
                            <div class="row g-2 align-items-end">
                                @if ($crudHasCodigo)
                                    <div class="col-md-4">
                                        <label class="form-label"
                                            for="crud_codigo_{{ $crudField }}">{{ __('Codigo') }}</label>
                                        <input type="text" id="crud_codigo_{{ $crudField }}"
                                            class="form-control" wire:model.live="crudCodigo"
                                            @if (!$this->isCrudRelationReady) disabled @endif>
                                        @error('crudCodigo')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                @endif

                                <div class="{{ $crudHasCodigo ? 'col-md-5' : 'col-md-9' }}">
                                    <label class="form-label"
                                        for="crud_nombre_{{ $crudField }}">{{ __('Nombre') }}</label>
                                    <input type="text" id="crud_nombre_{{ $crudField }}" class="form-control"
                                        wire:model.live="crudNombre" @if (!$this->isCrudRelationReady) disabled @endif>
                                    @error('crudNombre')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                @if ($crudHasEstado)
                                    <div class="col-md-3">
                                        <label class="form-label"
                                            for="crud_estado_{{ $crudField }}">{{ __('Estado') }}</label>
                                        <select id="crud_estado_{{ $crudField }}" class="form-select"
                                            wire:model.live="crudEstado"
                                            @if (!$this->isCrudRelationReady) disabled @endif>
                                            <option value="1">{{ __('Activo') }}</option>
                                            <option value="0">{{ __('Inactivo') }}</option>
                                        </select>
                                        @error('crudEstado')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                @endif
                            </div>

                            <div class="d-flex justify-content-between mt-3">
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-label-secondary"
                                        wire:click="startFieldCrudCreate">
                                        {{ __('Nuevo') }}
                                    </button>

                                    @if ($crudRecordId)
                                        <button type="button" class="btn btn-label-danger"
                                            wire:click="deleteFieldCrudRecord"
                                            onclick="return confirm('{{ __('Desea eliminar este registro?') }}')">
                                            {{ __('Eliminar') }}
                                        </button>
                                    @endif
                                </div>

                                <button type="submit" class="btn btn-primary"
                                    @if (!$this->isCrudRelationReady) disabled @endif>
                                    {{ $crudRecordId ? __('Actualizar') : __('Crear') }}
                                </button>
                            </div>
                        </form>

                        <div class="table-responsive border rounded">
                            <table class="table table-sm mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 5rem;">ID</th>
                                        @if ($crudHasCodigo)
                                            <th style="width: 12rem;">{{ __('Codigo') }}</th>
                                        @endif
                                        <th>{{ __('Nombre') }}</th>
                                        @if ($crudHasEstado)
                                            <th style="width: 6rem;">{{ __('Estado') }}</th>
                                        @endif
                                        <th style="width: 7rem;">{{ __('Accion') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($crudRows as $row)
                                        <tr class="{{ $crudRecordId === $row['id'] ? 'table-primary' : '' }}">
                                            <td>{{ $row['id'] }}</td>
                                            @if ($crudHasCodigo)
                                                <td>{{ $row['codigo'] }}</td>
                                            @endif
                                            <td>{{ $row['nombre'] }}</td>
                                            @if ($crudHasEstado)
                                                <td>
                                                    <span
                                                        class="badge {{ (int) $row['estado'] === 1 ? 'bg-label-success' : 'bg-label-secondary' }}">
                                                        {{ (int) $row['estado'] === 1 ? __('Activo') : __('Inactivo') }}
                                                    </span>
                                                </td>
                                            @endif
                                            <td>
                                                <button type="button" class="btn btn-sm btn-label-primary"
                                                    wire:click="editFieldCrudRecord({{ $row['id'] }})">
                                                    {{ __('Editar') }}
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ $crudHasCodigo && $crudHasEstado ? 5 : ($crudHasCodigo || $crudHasEstado ? 4 : 3) }}"
                                                class="text-center text-muted py-3">
                                                {{ __('Sin registros para este filtro.') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary"
                            wire:click="closeFieldCrudModal">{{ __('Cerrar') }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
