@extends('core::layouts.app')

@section('content')
<div class="container">
    <h1>Lista de Grupos de Empresa</h1>
    <a href="{{ route('core.grupo_empresa.create') }}" class="btn btn-primary mb-3">Crear Nuevo Grupo</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($grupos as $grupo)
            <tr>
                <td>{{ $grupo->id }}</td>
                <td>{{ $grupo->nombre }}</td>
                <td>{{ $grupo->descripcion }}</td>
                <td>
                    <a href="{{ route('core.grupo_empresa.show', $grupo->id) }}" class="btn btn-info btn-sm">Ver</a>
                    <a href="{{ route('core.grupo_empresa.edit', $grupo->id) }}" class="btn btn-warning btn-sm">Editar</a>
                    <form action="{{ route('core.grupo_empresa.destroy', $grupo->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar este grupo?')">Eliminar</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
