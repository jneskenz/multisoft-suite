@php
    $empresa = $empresa ?? new \Modules\Partners\Models\Empresa();
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
    <label for="ruc" class="form-label">{{ __('RUC') }}</label>
    <input type="text"
           id="ruc"
           name="ruc"
           value="{{ old('ruc', $empresa->ruc) }}"
           class="form-control @error('ruc') is-invalid @enderror"
           maxlength="20"
           required>
    @error('ruc')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="col-md-5">
    <label for="razon_social" class="form-label">{{ __('Razon social') }}</label>
    <input type="text"
           id="razon_social"
           name="razon_social"
           value="{{ old('razon_social', $empresa->razon_social) }}"
           class="form-control @error('razon_social') is-invalid @enderror"
           maxlength="200"
           required>
    @error('razon_social')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="col-md-4">
    <label for="nombre_comercial" class="form-label">{{ __('Nombre comercial') }}</label>
    <input type="text"
           id="nombre_comercial"
           name="nombre_comercial"
           value="{{ old('nombre_comercial', $empresa->nombre_comercial) }}"
           class="form-control @error('nombre_comercial') is-invalid @enderror"
           maxlength="200">
    @error('nombre_comercial')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="col-md-6">
    <label for="direccion" class="form-label">{{ __('Direccion') }}</label>
    <textarea id="direccion"
              name="direccion"
              rows="2"
              class="form-control @error('direccion') is-invalid @enderror">{{ old('direccion', $empresa->direccion) }}</textarea>
    @error('direccion')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="col-md-3">
    <label for="email" class="form-label">{{ __('Email') }}</label>
    <input type="email"
           id="email"
           name="email"
           value="{{ old('email', $empresa->email) }}"
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
           value="{{ old('telefono', $empresa->telefono) }}"
           class="form-control @error('telefono') is-invalid @enderror"
           maxlength="50">
    @error('telefono')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="col-md-3">
    <label for="estado" class="form-label">{{ __('Estado') }}</label>
    <select id="estado" name="estado" class="form-select @error('estado') is-invalid @enderror" required>
        <option value="1" @selected((string) old('estado', (int) ($empresa->estado ?? true)) === '1')>{{ __('Activo') }}</option>
        <option value="0" @selected((string) old('estado', (int) ($empresa->estado ?? true)) === '0')>{{ __('Inactivo') }}</option>
    </select>
    @error('estado')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="col-12 d-flex justify-content-end gap-2 mt-2">
    <a href="{{ route('partners.empresas.index') }}" class="btn btn-outline-secondary">{{ __('Cancelar') }}</a>
    <button type="submit" class="btn btn-primary">
        <i class="ti tabler-device-floppy me-1"></i>{{ $submitLabel }}
    </button>
</div>
