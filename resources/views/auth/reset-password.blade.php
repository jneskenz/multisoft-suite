@extends('layouts.auth')

@section('title', __('Nueva Contraseña'))

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
        <h4 class="mb-1">{{ __('Restablecer contraseña') }} 🔐</h4>
        <p class="mb-6">{{ __('Ingresa tu nueva contraseña') }}</p>

        <form method="POST" action="{{ route('password.update') }}" class="mb-4">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="mb-4">
                <label for="email" class="form-label">{{ __('Correo electrónico') }}</label>
                <input type="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       id="email" 
                       name="email" 
                       value="{{ old('email', $request->email) }}"
                       placeholder="tu@email.com" 
                       required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4 form-password-toggle">
                <label class="form-label" for="password">{{ __('Nueva contraseña') }}</label>
                <div class="input-group input-group-merge">
                    <input type="password" 
                           id="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           name="password"
                           placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" 
                           required>
                    <span class="input-group-text cursor-pointer"><i class="fa-regular fa-eye-slash"></i></span>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4 form-password-toggle">
                <label class="form-label" for="password_confirmation">{{ __('Confirmar contraseña') }}</label>
                <div class="input-group input-group-merge">
                    <input type="password" 
                           id="password_confirmation" 
                           class="form-control" 
                           name="password_confirmation"
                           placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" 
                           required>
                    <span class="input-group-text cursor-pointer"><i class="fa-regular fa-eye-slash"></i></span>
                </div>
            </div>

            <button type="submit" class="btn btn-primary d-grid w-100">
                {{ __('Restablecer contraseña') }}
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

@push('scripts')
<script>
    document.querySelectorAll('.form-password-toggle .input-group-text').forEach(function(el) {
        el.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            }
        });
    });
</script>
@endpush
