@extends('layouts.auth')

@section('title', __('Crear Cuenta'))

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
        <h4 class="mb-1">{{ __('Comienza tu aventura') }} 🚀</h4>
        <p class="mb-6">{{ __('Crea tu cuenta para empezar') }}</p>

        <form method="POST" action="{{ route('register') }}" class="mb-4">
            @csrf

            <div class="mb-4">
                <label for="name" class="form-label">{{ __('Nombre completo') }}</label>
                <input type="text" 
                       class="form-control @error('name') is-invalid @enderror" 
                       id="name" 
                       name="name" 
                       value="{{ old('name') }}"
                       placeholder="Tu nombre" 
                       autofocus 
                       required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="form-label">{{ __('Correo electrónico') }}</label>
                <input type="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       id="email" 
                       name="email" 
                       value="{{ old('email') }}"
                       placeholder="tu@email.com" 
                       required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4 form-password-toggle">
                <label class="form-label" for="password">{{ __('Contraseña') }}</label>
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

            <div class="mb-6 mt-4">
                <div class="form-check">
                    <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox" id="terms" name="terms" required>
                    <label class="form-check-label" for="terms">
                        {{ __('Acepto los') }}
                        <a href="#">{{ __('términos y condiciones') }}</a>
                    </label>
                    @error('terms')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <button type="submit" class="btn btn-primary d-grid w-100">
                {{ __('Crear Cuenta') }}
            </button>
        </form>

        <p class="text-center">
            <span>{{ __('¿Ya tienes cuenta?') }}</span>
            <a href="{{ route('login') }}">
                <span>{{ __('Iniciar sesión') }}</span>
            </a>
        </p>

        <!-- Language Switcher -->
        <div class="d-flex justify-content-center gap-2 mt-4">
            @php $currentLocale = $locale ?? app()->getLocale(); @endphp
            @foreach(['es' => '🇪🇸 Español', 'en' => '🇺🇸 English'] as $loc => $name)
                <a href="/{{ $loc }}/register" class="btn btn-sm {{ $currentLocale === $loc ? 'btn-primary' : 'btn-outline-secondary' }}">
                    {{ $name }}
                </a>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Toggle password visibility
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
