@extends('layouts.auth')

@section('title', __('Restablecer Contraseña'))

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
        <h4 class="mb-1">{{ __('¿Olvidaste tu contraseña?') }} 🔒</h4>
        <p class="mb-6">{{ __('Ingresa tu correo y te enviaremos instrucciones para restablecer tu contraseña') }}</p>

        @if (session('status'))
            <div class="alert alert-success mb-4" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="mb-4">
            @csrf

            <div class="mb-4">
                <label for="email" class="form-label">{{ __('Correo electrónico') }}</label>
                <input type="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       id="email" 
                       name="email" 
                       value="{{ old('email') }}"
                       placeholder="tu@email.com" 
                       autofocus 
                       required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary d-grid w-100">
                {{ __('Enviar enlace de recuperación') }}
            </button>
        </form>

        <div class="text-center">
            <a href="{{ route('login') }}" class="d-flex align-items-center justify-content-center gap-1">
                <i class="fa-solid fa-arrow-left"></i>
                {{ __('Volver al inicio de sesión') }}
            </a>
        </div>
    </div>
</div>
@endsection
