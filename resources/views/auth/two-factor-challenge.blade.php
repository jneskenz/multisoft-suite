@extends('layouts.auth')

@section('title', __('Autenticación de Dos Factores'))

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
        <h4 class="mb-1">{{ __('Verificación en dos pasos') }} 🔐</h4>
        
        <div id="code-form">
            <p class="mb-6">
                {{ __('Ingresa el código de 6 dígitos de tu aplicación de autenticación.') }}
            </p>

            <form method="POST" action="{{ route('two-factor.login') }}" class="mb-4">
                @csrf

                <div class="mb-4">
                    <label for="code" class="form-label">{{ __('Código de autenticación') }}</label>
                    <input type="text" 
                           class="form-control @error('code') is-invalid @enderror" 
                           id="code" 
                           name="code" 
                           placeholder="000000"
                           maxlength="6"
                           autofocus>
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary d-grid w-100">
                    {{ __('Verificar') }}
                </button>
            </form>

            <p class="text-center">
                <a href="javascript:void(0)" onclick="toggleRecoveryForm()">
                    {{ __('¿No tienes acceso a tu dispositivo? Usa un código de recuperación') }}
                </a>
            </p>
        </div>

        <div id="recovery-form" style="display: none;">
            <p class="mb-6">
                {{ __('Ingresa uno de tus códigos de recuperación de emergencia.') }}
            </p>

            <form method="POST" action="{{ route('two-factor.login') }}" class="mb-4">
                @csrf

                <div class="mb-4">
                    <label for="recovery_code" class="form-label">{{ __('Código de recuperación') }}</label>
                    <input type="text" 
                           class="form-control @error('recovery_code') is-invalid @enderror" 
                           id="recovery_code" 
                           name="recovery_code" 
                           placeholder="xxxxx-xxxxx">
                    @error('recovery_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary d-grid w-100">
                    {{ __('Verificar') }}
                </button>
            </form>

            <p class="text-center">
                <a href="javascript:void(0)" onclick="toggleRecoveryForm()">
                    {{ __('Usar código de autenticación') }}
                </a>
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleRecoveryForm() {
        const codeForm = document.getElementById('code-form');
        const recoveryForm = document.getElementById('recovery-form');
        
        if (codeForm.style.display === 'none') {
            codeForm.style.display = 'block';
            recoveryForm.style.display = 'none';
        } else {
            codeForm.style.display = 'none';
            recoveryForm.style.display = 'block';
        }
    }
</script>
@endpush
