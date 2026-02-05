<div class="min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h1 class="text-center text-3xl font-extrabold text-gray-900 dark:text-white">
                {{ config('app.name') }}
            </h1>
            <h2 class="mt-6 text-center text-2xl font-bold text-gray-900 dark:text-white">
                {{ __('Crear Cuenta') }}
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
                {{ __('¿Ya tienes cuenta?') }}
                <a href="{{ route('login', ['locale' => app()->getLocale()]) }}" 
                   class="font-medium text-blue-600 hover:text-blue-500" wire:navigate>
                    {{ __('Inicia sesión') }}
                </a>
            </p>
        </div>

        <form wire:submit="register" class="mt-8 space-y-6 bg-white dark:bg-gray-800 p-8 rounded-lg shadow">
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('Nombre completo') }}
                </label>
                <input wire:model="name" 
                       id="name" 
                       type="text" 
                       autocomplete="name"
                       required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm 
                              placeholder-gray-400 dark:placeholder-gray-500
                              focus:outline-none focus:ring-blue-500 focus:border-blue-500 
                              dark:bg-gray-700 dark:text-white sm:text-sm"
                       placeholder="Tu nombre">
                @error('name')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('Correo electrónico') }}
                </label>
                <input wire:model="email" 
                       id="email" 
                       type="email" 
                       autocomplete="email"
                       required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm 
                              placeholder-gray-400 dark:placeholder-gray-500
                              focus:outline-none focus:ring-blue-500 focus:border-blue-500 
                              dark:bg-gray-700 dark:text-white sm:text-sm"
                       placeholder="tu@email.com">
                @error('email')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('Contraseña') }}
                </label>
                <input wire:model="password" 
                       id="password" 
                       type="password" 
                       autocomplete="new-password"
                       required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm 
                              placeholder-gray-400 dark:placeholder-gray-500
                              focus:outline-none focus:ring-blue-500 focus:border-blue-500 
                              dark:bg-gray-700 dark:text-white sm:text-sm"
                       placeholder="••••••••">
                @error('password')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('Confirmar contraseña') }}
                </label>
                <input wire:model="password_confirmation" 
                       id="password_confirmation" 
                       type="password" 
                       autocomplete="new-password"
                       required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm 
                              placeholder-gray-400 dark:placeholder-gray-500
                              focus:outline-none focus:ring-blue-500 focus:border-blue-500 
                              dark:bg-gray-700 dark:text-white sm:text-sm"
                       placeholder="••••••••">
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium 
                               rounded-md text-white bg-blue-600 hover:bg-blue-700 
                               focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
                               disabled:opacity-50 disabled:cursor-not-allowed"
                        wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="register">
                        {{ __('Crear Cuenta') }}
                    </span>
                    <span wire:loading wire:target="register" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ __('Procesando...') }}
                    </span>
                </button>
            </div>
        </form>

        <!-- Language Switcher -->
        <div class="flex justify-center space-x-4 mt-4">
            @foreach(supported_locales() as $locale => $info)
                <a href="/{{ $locale }}/register" 
                   class="text-sm {{ app()->getLocale() === $locale ? 'text-blue-600 font-semibold' : 'text-gray-500 hover:text-gray-700' }}">
                    {{ locale_flag($locale) }} {{ locale_name($locale) }}
                </a>
            @endforeach
        </div>
    </div>
</div>
