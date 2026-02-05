@extends('layouts.auth')

@section('title', __('Verificación de Correo'))

@section('content')
<!-- Logo -->
<div class="app-brand justify-content-center mb-6">
    <a href="/" class="app-brand-link">
        <span class="app-brand-logo demo">
            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M16 2L2 9L16 16L30 9L16 2Z" fill="#7367f0"/>
                <path d="M2 23L16 30L30 23V9L16 16L2 9V23Z" fill="#7367f0" fill-opacity="0.5"/>
            </svg>
        </span>
        <span class="app-brand-text demo text-heading fw-bold">{{ config('app.name') }}</span>
    </a>
</div>
<!-- /Logo -->

<div class="card">
    <div class="card-body">
        <h4 class="mb-1">{{ __('Verifica tu correo') }} ✉️</h4>
        <p class="mb-6">
            {{ __('Hemos enviado un enlace de verificación a tu correo electrónico. Por favor revisa tu bandeja de entrada.') }}
        </p>

        @if (session('status') == 'verification-link-sent')
            <div class="alert alert-success mb-4" role="alert">
                {{ __('Se ha enviado un nuevo enlace de verificación a tu correo electrónico.') }}
            </div>
        @endif

        <div class="d-flex flex-column gap-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn btn-primary d-grid w-100">
                    {{ __('Reenviar correo de verificación') }}
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-secondary d-grid w-100">
                    {{ __('Cerrar sesión') }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
