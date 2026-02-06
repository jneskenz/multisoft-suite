@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Grupo de Empresa</h1>
    <form action="{{ route('grupo_empresa.update', $grupo->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $grupo->nombre }}" required>
        </div>
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion">{{ $grupo->descripcion }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="{{ route('grupo_empresa.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
