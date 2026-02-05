@extends('layouts.auth')

@section('title', __('Confirmar Contraseña'))

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
        <h4 class="mb-1">{{ __('Confirma tu contraseña') }} 🔐</h4>
        <p class="mb-6">
            {{ __('Esta es un área segura. Por favor confirma tu contraseña antes de continuar.') }}
        </p>

        <form method="POST" action="{{ route('password.confirm') }}" class="mb-4">
            @csrf

            <div class="mb-4 form-password-toggle">
                <label class="form-label" for="password">{{ __('Contraseña') }}</label>
                <div class="input-group input-group-merge">
                    <input type="password" 
                           id="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           name="password"
                           placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" 
                           required
                           autofocus>
                    <span class="input-group-text cursor-pointer"><i class="fa-regular fa-eye-slash"></i></span>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <button type="submit" class="btn btn-primary d-grid w-100">
                {{ __('Confirmar') }}
            </button>
        </form>
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
