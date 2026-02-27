@php
    $personas = $personas ?? collect();
    $showActions = auth()->user()?->can('partners.edit') || auth()->user()?->can('partners.delete');
@endphp

<div class="table-responsive">
    <table class="table table-sm align-middle">
        <thead>
            <tr>
                <th class="text-center" style="width: 130px;">{{ __('Acciones') }}</th>
                <th>{{ __('Persona') }}</th>
                <th style="width: 13rem;">{{ __('Documento') }}</th>
                <th style="width: 16rem;">{{ __('Contacto') }}</th>
                <th style="width: 18rem;">{{ __('Tipos') }}</th>
                <th style="width: 8rem;">{{ __('Empresas') }}</th>
                <th style="width: 7rem;">{{ __('Estado') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($personas as $persona)
                @php
                    $nombre = trim((string) ($persona->nombre_completo ?? ''));
                    if ($nombre === '') {
                        $nombre = trim(implode(' ', array_filter([
                            $persona->nombres ?? '',
                            $persona->apellido_paterno ?? '',
                            $persona->apellido_materno ?? '',
                        ])));
                    }

                    $tipos = collect($persona->tipos ?? [])
                        ->filter(fn ($item) => (bool) ($item->estado ?? true))
                        ->pluck('tipo')
                        ->map(fn ($item) => ucfirst((string) $item))
                        ->values();

                    $doc = trim((string) (($persona->tipo_documento ?? '') . ' ' . ($persona->numero_documento ?? '')));
                    $contacto = trim(implode(' · ', array_filter([
                        (string) ($persona->email ?? ''),
                        (string) ($persona->telefono ?? ''),
                    ])));
                @endphp
                <tr>
                    <td>
                        <div class="d-flex justify-content-start align-items-center gap-1">
                            <button
                                class="btn btn-sm rounded-pill btn-icon btn-label-secondary waves-effect me-1"
                                data-bs-toggle="tooltip" title="{{ __('Nro de registro: :id', ['id' => $persona->id]) }}">
                                {{ $persona->id }}
                            </button>

                            @can('partners.edit')
                                <a href="{{ route('partners.personas.edit', $persona->id) }}"
                                   class="btn btn-sm btn-icon btn-label-primary"
                                   data-bs-toggle="tooltip"
                                   title="{{ __('Editar persona') }}">
                                    <i class="ti tabler-edit"></i>
                                </a>
                            @endcan

                            @can('partners.delete')
                                <form class="d-inline"
                                      action="{{ route('partners.personas.destroy', $persona->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('{{ __('Estas seguro de eliminar esta persona?') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-sm btn-icon btn-label-danger"
                                            data-bs-toggle="tooltip"
                                            title="{{ __('Eliminar persona') }}">
                                        <i class="ti tabler-trash"></i>
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </td>
                    <td>{{ $nombre !== '' ? $nombre : '-' }}</td>
                    <td>{{ $doc !== '' ? $doc : '-' }}</td>
                    <td>{{ $contacto !== '' ? $contacto : '-' }}</td>
                    <td>
                        @forelse ($tipos as $tipo)
                            <span class="badge bg-label-primary me-1">{{ $tipo }}</span>
                        @empty
                            <span class="text-muted">-</span>
                        @endforelse
                    </td>
                    <td>{{ collect($persona->empresas ?? [])->count() }}</td>
                    <td>
                        <span class="badge {{ (bool) ($persona->estado ?? false) ? 'bg-label-success' : 'bg-label-secondary' }}">
                            {{ (bool) ($persona->estado ?? false) ? __('Activo') : __('Inactivo') }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-3">{{ __('Sin registros.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
