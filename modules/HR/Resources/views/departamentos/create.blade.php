@extends('layouts.app')

@section('title', __('Crear Departamento'))

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="mb-2">{{ __('Crear Departamento') }}</h5>
            <p class="text-muted mb-4">
                {{ __('La gestion de departamentos se realiza desde el listado principal.') }}
            </p>
            <a href="{{ route('hr.configuracion.departamentos.index') }}" class="btn btn-primary">
                <i class="ti tabler-arrow-left me-1"></i>{{ __('Volver al listado') }}
            </a>
        </div>
    </div>
@endsection
