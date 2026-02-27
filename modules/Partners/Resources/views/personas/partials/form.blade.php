@php
    $persona = $persona ?? new \Modules\Partners\Models\Persona();
    $tiposDisponibles = $tiposDisponibles ?? [];
    $tiposSeleccionados = old('tipos', $tiposSeleccionados ?? []);
    $tiposSeleccionados = is_array($tiposSeleccionados) ? $tiposSeleccionados : [];
    $submitLabel = $submitLabel ?? __('Guardar');
@endphp

@if ($errors->any())
    <div class="col-12">
        <div class="alert alert-danger mb-1">
            <strong>{{ __('Revisa los campos del formulario.') }}</strong>
        </div>
    </div>
@endif

<div class="col-md-3">
    <label for="tipo_documento" class="form-label">{{ __('Tipo documento') }}</label>
    <input type="text"
           id="tipo_documento"
           name="tipo_documento"
           value="{{ old('tipo_documento', $persona->tipo_documento) }}"
           class="form-control @error('tipo_documento') is-invalid @enderror"
           maxlength="20">
    @error('tipo_documento')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="col-md-3">
    <label for="numero_documento" class="form-label">{{ __('Numero documento') }}</label>
    <input type="text"
           id="numero_documento"
           name="numero_documento"
           value="{{ old('numero_documento', $persona->numero_documento) }}"
           class="form-control @error('numero_documento') is-invalid @enderror"
           maxlength="30">
    @error('numero_documento')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="col-md-3">
    <label for="nombres" class="form-label">{{ __('Nombres') }}</label>
    <input type="text"
           id="nombres"
           name="nombres"
           value="{{ old('nombres', $persona->nombres) }}"
           class="form-control @error('nombres') is-invalid @enderror"
           maxlength="120"
           required>
    @error('nombres')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="col-md-3">
    <label for="apellido_paterno" class="form-label">{{ __('Apellido paterno') }}</label>
    <input type="text"
           id="apellido_paterno"
           name="apellido_paterno"
           value="{{ old('apellido_paterno', $persona->apellido_paterno) }}"
           class="form-control @error('apellido_paterno') is-invalid @enderror"
           maxlength="120">
    @error('apellido_paterno')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="col-md-3">
    <label for="apellido_materno" class="form-label">{{ __('Apellido materno') }}</label>
    <input type="text"
           id="apellido_materno"
           name="apellido_materno"
           value="{{ old('apellido_materno', $persona->apellido_materno) }}"
           class="form-control @error('apellido_materno') is-invalid @enderror"
           maxlength="120">
    @error('apellido_materno')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="col-md-6">
    <label for="nombre_completo" class="form-label">{{ __('Nombre completo') }}</label>
    <input type="text"
           id="nombre_completo"
           name="nombre_completo"
           value="{{ old('nombre_completo', $persona->nombre_completo) }}"
           class="form-control @error('nombre_completo') is-invalid @enderror"
           maxlength="255">
    @error('nombre_completo')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="col-md-3">
    <label for="email" class="form-label">{{ __('Email') }}</label>
    <input type="email"
           id="email"
           name="email"
           value="{{ old('email', $persona->email) }}"
           class="form-control @error('email') is-invalid @enderror"
           maxlength="100">
    @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="col-md-3">
    <label for="telefono" class="form-label">{{ __('Telefono') }}</label>
    <input type="text"
           id="telefono"
           name="telefono"
           value="{{ old('telefono', $persona->telefono) }}"
           class="form-control @error('telefono') is-invalid @enderror"
           maxlength="50">
    @error('telefono')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="col-md-3">
    <label for="fecha_nacimiento" class="form-label">{{ __('Fecha nacimiento') }}</label>
    <input type="date"
           id="fecha_nacimiento"
           name="fecha_nacimiento"
           value="{{ old('fecha_nacimiento', optional($persona->fecha_nacimiento)->format('Y-m-d')) }}"
           class="form-control @error('fecha_nacimiento') is-invalid @enderror">
    @error('fecha_nacimiento')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="col-md-9">
    <label class="form-label d-block">{{ __('Tipos de persona') }}</label>
    <div class="d-flex flex-wrap gap-3">
        @foreach ($tiposDisponibles as $tipo)
            <div class="form-check">
                <input class="form-check-input"
                       type="checkbox"
                       id="tipo_{{ $tipo }}"
                       name="tipos[]"
                       value="{{ $tipo }}"
                       @checked(in_array($tipo, $tiposSeleccionados, true))>
                <label class="form-check-label" for="tipo_{{ $tipo }}">
                    {{ ucfirst($tipo) }}
                </label>
            </div>
        @endforeach
    </div>
    @error('tipos')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
    @error('tipos.*')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>

<div class="col-md-3">
    <label for="estado" class="form-label">{{ __('Estado') }}</label>
    <select id="estado" name="estado" class="form-select @error('estado') is-invalid @enderror" required>
        <option value="1" @selected((string) old('estado', (int) ($persona->estado ?? true)) === '1')>{{ __('Activo') }}</option>
        <option value="0" @selected((string) old('estado', (int) ($persona->estado ?? true)) === '0')>{{ __('Inactivo') }}</option>
    </select>
    @error('estado')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="col-12 d-flex justify-content-end gap-2 mt-2">
    <a href="{{ route('partners.personas.index') }}" class="btn btn-outline-secondary">{{ __('Cancelar') }}</a>
    <button type="submit" class="btn btn-primary">
        <i class="ti tabler-device-floppy me-1"></i>{{ $submitLabel }}
    </button>
</div>
