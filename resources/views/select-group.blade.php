@extends('layouts.app')

@section('title', __('Seleccionar Grupo'))

@section('content')
<!-- Header -->
<div class="row mb-6">
    <div class="col-12">
        <div class="card bg-transparent shadow-none border-0">
            <div class="card-body p-0">
                <h4 class="mb-1">{{ __('Selecciona tu grupo de trabajo') }}</h4>
                <p class="mb-0 text-muted">{{ __('Tienes acceso a múltiples operaciones. Elige con cuál deseas trabajar.') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Group Cards -->
<div class="row g-6">
    @foreach($groups as $group)
    <div class="col-xl-4 col-lg-6 col-md-6">
        <div class="card h-100 card-hover-shadow">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    {{-- Flag Emoji --}}
                    <div class="avatar avatar-lg flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-primary" style="font-size: 1.5rem;">
                            {{ $group->flag_emoji }}
                        </span>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ $group->display_name }}</h5>
                        <small class="text-muted">{{ $group->code }}</small>
                    </div>
                </div>

                {{-- Group Details --}}
                <ul class="list-unstyled mb-4">
                    <li class="d-flex align-items-center mb-2">
                        <i class="ti tabler-building-skyscraper me-2 text-muted"></i>
                        <span class="text-muted">{{ $group->business_name }}</span>
                    </li>
                    @if($group->tax_id)
                    <li class="d-flex align-items-center mb-2">
                        <i class="ti tabler-file-certificate me-2 text-muted"></i>
                        <span class="text-muted">{{ $group->tax_id }}</span>
                    </li>
                    @endif
                    <li class="d-flex align-items-center mb-2">
                        <i class="ti tabler-clock me-2 text-muted"></i>
                        <span class="text-muted">{{ $group->timezone }}</span>
                    </li>
                    <li class="d-flex align-items-center">
                        <i class="ti tabler-currency-dollar me-2 text-muted"></i>
                        <span class="text-muted">{{ $group->currency }}</span>
                    </li>
                </ul>
            </div>
            <div class="card-footer border-top">
                <a href="{{ url(app()->getLocale() . '/' . $group->code . '/welcome') }}" class="btn btn-primary w-100">
                    <i class="ti tabler-arrow-right me-2"></i>{{ __('Ingresar') }}
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>

@if($groups->isEmpty())
<div class="row">
    <div class="col-12">
        <div class="alert alert-warning">
            <div class="d-flex align-items-center">
                <i class="ti tabler-alert-triangle me-2"></i>
                <div>
                    <h6 class="mb-0">{{ __('Sin acceso a grupos') }}</h6>
                    <small>{{ __('No tienes acceso a ningún grupo de empresas. Contacta al administrador.') }}</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
