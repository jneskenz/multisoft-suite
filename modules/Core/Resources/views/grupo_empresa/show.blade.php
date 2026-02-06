@extends('core::layouts.app')

@section('content')
<div class="container">
    <h1>Detalle del Grupo de Empresa</h1>
    <div class="mb-3">
        <label class="form-label"><strong>ID:</strong></label>
        <div>{{ $grupo->id }}</div>
    </div>
    <div class="mb-3">
        <label class="form-label"><strong>Nombre:</strong></label>
        <div>{{ $grupo->nombre }}</div>
    </div>
    <div class="mb-3">
        <label class="form-label"><strong>Descripción:</strong></label>
        <div>{{ $grupo->descripcion }}</div>
    </div>
    <a href="{{ route('core.grupo_empresa.index') }}" class="btn btn-secondary">Volver</a>
    <a href="{{ route('core.grupo_empresa.edit', $grupo->id) }}" class="btn btn-warning">Editar</a>
</div>
@endsection
