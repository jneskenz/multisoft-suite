@php
    /** @var \Illuminate\Support\Collection<int,object>|array<int,object> $rows */
    $rows = $rows ?? collect();
    $categoriaId = (int) ($categoriaId ?? 0);
    $categoriaNombre = (string) ($categoriaNombre ?? '');
    $categoriasConMedidas = array_map('strtoupper', (array) ($categoriasConMedidas ?? ['LTE', 'LST', 'LCT']));
    $categoriaCodigoFallback = strtoupper((string) ($categoriaCodigo ?? ''));
    $detailModalId = 'catalogoDetailModal_' . $categoriaId;
@endphp

<div data-catalogo-table-wrapper="1" data-categoria-id="{{ $categoriaId }}">
    <div class="row g-2 my-3">
        <div class="col-md-3">
            <label class="form-label mb-1">{{ __('Codigo') }}</label>
            <input type="text" class="form-control form-control-sm" placeholder="{{ __('Filtrar por codigo') }}"
                data-filter-codigo>
        </div>
        <div class="col-md-3">
            <label class="form-label mb-1">{{ __('Subcategoria') }}</label>
            <input type="text" class="form-control form-control-sm"
                placeholder="{{ __('Filtrar por subcategoria') }}" data-filter-subcategoria>
        </div>
        <div class="col-md-3">
            <label class="form-label mb-1">{{ __('Descripcion') }}</label>
            <input type="text" class="form-control form-control-sm"
                placeholder="{{ __('Filtrar por descripcion') }}" data-filter-descripcion>
        </div>
        <div class="col-md-2">
            <label class="form-label mb-1">{{ __('Estado') }}</label>
            <select class="form-select form-select-sm" data-filter-estado>
                <option value="">{{ __('Todos') }}</option>
                <option value="1">{{ __('Activo') }}</option>
                <option value="0">{{ __('Inactivo') }}</option>
            </select>
        </div>
        <div class="col-md-1 d-flex align-items-end">
            <button type="button" class="btn btn-md btn-outline-secondary w-100" data-filter-clear="1">
                <i class="ti tabler-x"></i>
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-sm align-middle">
            <thead>
                <tr>
                    <th style="width: 13rem;">{{ __('Acciones') }}</th>
                    <th style="width: 10rem;">{{ __('Codigo') }}</th>
                    <th style="width: 12rem;">{{ __('Subcategoria') }}</th>
                    <th>{{ __('Descripcion') }}</th>
                    <th style="width: 7rem;">{{ __('Estado') }}</th>
                    <th style="width: 11rem;">{{ __('Fecha') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    @php
                        $estadoValue = (int) ($row->estado ?? 1);
                        $estadoLabel = $estadoValue === 1 ? __('Activo') : __('Inactivo');
                        $rowCategoriaCodigo = strtoupper(trim((string) ($row->categoria_codigo ?? $categoriaCodigoFallback)));
                        $showMedidasButton = in_array($rowCategoriaCodigo, $categoriasConMedidas, true);
                        $fechaLabel = $row->created_at
                            ? \Illuminate\Support\Carbon::parse($row->created_at)->format('Y-m-d H:i')
                            : '-';
                    @endphp
                    <tr data-catalogo-row="1"
                        data-codigo="{{ \Illuminate\Support\Str::lower((string) ($row->codigo ?? '')) }}"
                        data-subcategoria="{{ \Illuminate\Support\Str::lower((string) ($row->subcategoria_nombre ?? '')) }}"
                        data-descripcion="{{ \Illuminate\Support\Str::lower((string) ($row->descripcion ?? '')) }}"
                        data-estado="{{ $estadoValue }}">
                        <td>
                            <div class="d-flex justify-content-start align-items-center gap-1">
                                <button class="btn btn-sm rounded-pill btn-icon btn-label-secondary waves-effect me-1"
                                    data-bs-toggle="tooltip" title="{{ __('Nro de registro') }}: {{ $row->id }}">
                                    {{ $row->id }}
                                </button>
                                <button type="button" class="btn btn-sm btn-icon btn-label-primary"
                                    data-catalogo-edit="1" data-registro-id="{{ $row->id }}"
                                    data-categoria-id="{{ $categoriaId }}" data-categoria-nombre="{{ $categoriaNombre }}"
                                    data-bs-toggle="tooltip" title="{{ __('Editar catalogo') }}">
                                    <i class="ti tabler-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-icon btn-label-warning"
                                    data-catalogo-view="1" data-bs-toggle="modal"
                                    data-bs-target="#{{ $detailModalId }}" data-detail-id="{{ $row->id }}"
                                    data-detail-categoria="{{ $categoriaNombre }}"
                                    data-detail-codigo="{{ (string) ($row->codigo ?? '-') }}"
                                    data-detail-subcategoria="{{ (string) ($row->subcategoria_nombre ?? '-') }}"
                                    data-detail-descripcion="{{ (string) ($row->descripcion ?? '-') }}"
                                    data-detail-estado="{{ $estadoValue }}"
                                    data-detail-estado-label="{{ $estadoLabel }}"
                                    data-detail-fecha="{{ $fechaLabel }}"
                                    title="{{ __('Ver detalle') }}">
                                    <i class="ti tabler-eye"></i>
                                </button>
                                @if($showMedidasButton)
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        data-catalogo-medidas="1"
                                        data-registro-id="{{ $row->id }}"
                                        data-categoria-id="{{ (int) ($row->categoria_id ?? $categoriaId) }}"
                                        data-categoria-codigo="{{ $rowCategoriaCodigo }}"
                                        data-categoria-nombre="{{ $categoriaNombre }}"
                                        data-articulo-codigo="{{ (string) ($row->codigo ?? '-') }}"
                                        data-articulo-subcategoria="{{ (string) ($row->subcategoria_nombre ?? '-') }}"
                                        data-articulo-descripcion="{{ (string) ($row->descripcion ?? '-') }}"
                                        data-bs-toggle="tooltip"
                                        title="{{ __('Gestionar medidas y stock') }}">
                                        <i class="ti tabler-ruler-2 me-1"></i>{{ __('Medidas') }}
                                    </button>
                                @endif
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-icon border btn-text-secondary rounded dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="ti tabler-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item d-flex p-2 justify-content-start btn btn-label-info align-items-center"
                                                href="javascript:void(0);" data-catalogo-duplicate="1"
                                                data-registro-id="{{ $row->id }}" data-categoria-id="{{ $categoriaId }}"
                                                data-categoria-nombre="{{ $categoriaNombre }}"
                                                data-bs-toggle="tooltip" data-bs-custom-class="tooltip-info"
                                                title="{{ __('Duplicar catalogo') }}">
                                                <button class="btn btn-sm btn-icon btn-label-info me-2" type="button">
                                                    <i class="ti tabler-copy"></i>
                                                </button>
                                                <span class="lh-1">{{ __('Duplicar') }}</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex p-2 justify-content-start btn btn-label-danger align-items-center"
                                                href="javascript:void(0);" data-catalogo-delete="1"
                                                data-registro-id="{{ $row->id }}" data-categoria-id="{{ $categoriaId }}"
                                                data-bs-toggle="tooltip" data-bs-custom-class="tooltip-danger"
                                                title="{{ __('Eliminar catalogo') }}">
                                                <button class="btn btn-sm btn-icon btn-label-danger me-2" type="button">
                                                    <i class="ti tabler-trash"></i>
                                                </button>
                                                <span class="lh-1">{{ __('Eliminar') }}</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                        <td>{{ $row->codigo }}</td>
                        <td>{{ $row->subcategoria_nombre ?? '-' }}</td>
                        <td>{{ $row->descripcion }}</td>
                        <td>
                            <span class="badge {{ (int) $row->estado === 1 ? 'bg-label-success' : 'bg-label-secondary' }}">
                                {{ (int) $row->estado === 1 ? __('Activo') : __('Inactivo') }}
                            </span>
                        </td>
                        <td>
                            {{ $fechaLabel }}
                        </td>
                    </tr>
                @empty
                    <tr data-catalogo-empty-row="1">
                        <td colspan="6" class="text-center text-muted py-3">
                            {{ __('Sin registros.') }}
                        </td>
                    </tr>
                @endforelse
                @if (count($rows) > 0)
                    <tr data-catalogo-empty-row="1" style="display: none;">
                        <td colspan="6" class="text-center text-muted py-3">
                            {{ __('Sin registros para este filtro.') }}
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="modal fade"  id="{{ $detailModalId }}" tabindex="-1" aria-hidden="true"
        data-catalogo-detail-modal="1" data-categoria-nombre="{{ $categoriaNombre }}">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="ti tabler-eye me-2"></i>{{ __('Detalle de Catalogo') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Cerrar') }}"></button>
                </div>
                <div class="modal-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between px-0">
                            <strong>{{ __('ID') }}</strong>
                            <span data-detail-target="id">-</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between px-0">
                            <strong>{{ __('Categoria') }}</strong>
                            <span data-detail-target="categoria">{{ $categoriaNombre !== '' ? $categoriaNombre : '-' }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between px-0">
                            <strong>{{ __('Codigo') }}</strong>
                            <span data-detail-target="codigo">-</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between px-0">
                            <strong>{{ __('Subcategoria') }}</strong>
                            <span data-detail-target="subcategoria">-</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between px-0">
                            <strong>{{ __('Estado') }}</strong>
                            <span class="badge bg-label-secondary" data-detail-target="estado">-</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between px-0">
                            <strong>{{ __('Fecha') }}</strong>
                            <span data-detail-target="fecha">-</span>
                        </div>
                        <div class="list-group-item px-0">
                            <strong class="d-block mb-2">{{ __('Descripcion') }}</strong>
                            <div class="text-muted" style="white-space: pre-wrap;" data-detail-target="descripcion">-</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                        {{ __('Cerrar') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
