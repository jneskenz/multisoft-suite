@php
    $relacion = $relacion ?? new \Modules\Partners\Models\PersonaEmpresa();
    $personas = $personas ?? collect();
    $empresas = $empresas ?? collect();
    $tiposRelacion = $tiposRelacion ?? [];
    $submitLabel = $submitLabel ?? __('Guardar');

    $personaId = (int) old('persona_id', $relacion->persona_id);
    $empresaId = (int) old('empresa_id', $relacion->empresa_id);
    $tipoRelacion = (string) old('tipo_relacion', $relacion->tipo_relacion ?: 'contacto');
    $esPrincipal = (string) old('es_principal', (int) ($relacion->es_principal ?? false));
    $estado = (string) old('estado', (int) ($relacion->estado ?? true));
    $canSubmit = $personas->isNotEmpty() && $empresas->isNotEmpty();
@endphp

@if ($errors->any())
    <div class="col-12">
        <div class="alert alert-danger mb-1">
            <strong>{{ __('Revisa los campos del formulario.') }}</strong>
        </div>
    </div>
@endif

@if (!$canSubmit)
    <div class="col-12">
        <div class="alert alert-warning mb-1">
            {{ __('Debes tener al menos una persona y una empresa activas para registrar una relacion.') }}
        </div>
    </div>
@endif

<div class="col-md-6">
    <label for="persona_id" class="form-label">{{ __('Persona') }}</label>
    <select id="persona_id" name="persona_id" class="form-select @error('persona_id') is-invalid @enderror" required>
        <option value="">{{ __('Selecciona una persona') }}</option>
        @foreach ($personas as $persona)
            @php
                $nombre = trim((string) ($persona->nombre_completo ?? ''));
                if ($nombre === '') {
                    $nombre = trim(implode(' ', array_filter([
                        $persona->nombres ?? '',
                        $persona->apellido_paterno ?? '',
                        $persona->apellido_materno ?? '',
                    ])));
                }
            @endphp
            <option value="{{ $persona->id }}" @selected($personaId === (int) $persona->id)>
                {{ $nombre !== '' ? $nombre : __('Persona') . ' #' . $persona->id }}
            </option>
        @endforeach
    </select>
    @error('persona_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="col-md-6">
    <label for="empresa_id" class="form-label">{{ __('Empresa') }}</label>
    <select id="empresa_id" name="empresa_id" class="form-select @error('empresa_id') is-invalid @enderror" required>
        <option value="">{{ __('Selecciona una empresa') }}</option>
        @foreach ($empresas as $empresa)
            @php
                $empresaLabel = trim((string) ($empresa->razon_social ?? ''));
                if ($empresaLabel !== '' && trim((string) ($empresa->ruc ?? '')) !== '') {
                    $empresaLabel .= ' (RUC: ' . $empresa->ruc . ')';
                }
            @endphp
            <option value="{{ $empresa->id }}" @selected($empresaId === (int) $empresa->id)>
                {{ $empresaLabel !== '' ? $empresaLabel : __('Empresa') . ' #' . $empresa->id }}
            </option>
        @endforeach
    </select>
    @error('empresa_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="col-md-4">
    <label for="tipo_relacion" class="form-label">{{ __('Tipo relacion') }}</label>
    <select id="tipo_relacion" name="tipo_relacion" class="form-select @error('tipo_relacion') is-invalid @enderror" required>
        @foreach ($tiposRelacion as $tipo)
            <option value="{{ $tipo }}" @selected($tipoRelacion === $tipo)>{{ ucfirst($tipo) }}</option>
        @endforeach
    </select>
    @error('tipo_relacion')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="col-md-4">
    <label for="es_principal" class="form-label">{{ __('Relacion principal') }}</label>
    <select id="es_principal" name="es_principal" class="form-select @error('es_principal') is-invalid @enderror" required>
        <option value="1" @selected($esPrincipal === '1')>{{ __('Si') }}</option>
        <option value="0" @selected($esPrincipal === '0')>{{ __('No') }}</option>
    </select>
    <div class="form-text">{{ __('Si es principal, se desmarcan las otras relaciones principales de esa persona.') }}</div>
    @error('es_principal')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="col-md-4">
    <label for="estado" class="form-label">{{ __('Estado') }}</label>
    <select id="estado" name="estado" class="form-select @error('estado') is-invalid @enderror" required>
        <option value="1" @selected($estado === '1')>{{ __('Activo') }}</option>
        <option value="0" @selected($estado === '0')>{{ __('Inactivo') }}</option>
    </select>
    @error('estado')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="col-12 d-flex justify-content-end gap-2 mt-2">
    <a href="{{ route('partners.relaciones.index') }}" class="btn btn-outline-secondary">{{ __('Cancelar') }}</a>
    <button type="submit" class="btn btn-primary" @disabled(!$canSubmit)>
        <i class="ti tabler-device-floppy me-1"></i>{{ $submitLabel }}
    </button>
</div>
