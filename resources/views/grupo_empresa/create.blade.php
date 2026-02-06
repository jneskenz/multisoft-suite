@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Crear Grupo de Empresa</h1>
    <form action="{{ route('grupo_empresa.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion"></textarea>
        </div>
        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="{{ route('grupo_empresa.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
