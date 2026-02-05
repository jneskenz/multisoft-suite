<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">
    <div class="min-h-screen">
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 w-64 bg-gray-800 text-white">
            <div class="p-4 border-b border-gray-700">
                <h1 class="text-xl font-bold">{{ config('app.name') }}</h1>
            </div>
            <nav class="p-4">
                <ul class="space-y-2">
                    <li>
                        <a href="{{ url(app()->getLocale()) }}" 
                           class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->is(app()->getLocale()) ? 'bg-gray-700' : '' }}">
                            📊 Dashboard
                        </a>
                    </li>
                    @can('access.partners')
                    <li>
                        <a href="{{ url(app()->getLocale() . '/partners') }}" 
                           class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->is('*/partners*') ? 'bg-gray-700' : '' }}">
                            👥 Partners
                        </a>
                    </li>
                    @endcan
                    @can('access.erp')
                    <li>
                        <a href="{{ url(app()->getLocale() . '/erp') }}" 
                           class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->is('*/erp*') ? 'bg-gray-700' : '' }}">
                            🏢 ERP
                        </a>
                    </li>
                    @endcan
                    @can('access.fms')
                    <li>
                        <a href="{{ url(app()->getLocale() . '/fms') }}" 
                           class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->is('*/fms*') ? 'bg-gray-700' : '' }}">
                            💰 FMS
                        </a>
                    </li>
                    @endcan
                    @can('access.hr')
                    <li>
                        <a href="{{ url(app()->getLocale() . '/hr') }}" 
                           class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->is('*/hr*') ? 'bg-gray-700' : '' }}">
                            👤 HR
                        </a>
                    </li>
                    @endcan
                    @can('access.crm')
                    <li>
                        <a href="{{ url(app()->getLocale() . '/crm') }}" 
                           class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->is('*/crm*') ? 'bg-gray-700' : '' }}">
                            📈 CRM
                        </a>
                    </li>
                    @endcan
                    @can('access.reports')
                    <li>
                        <a href="{{ url(app()->getLocale() . '/reports') }}" 
                           class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->is('*/reports*') ? 'bg-gray-700' : '' }}">
                            📑 Reports
                        </a>
                    </li>
                    @endcan
                    @can('access.core')
                    <li class="pt-4 border-t border-gray-700 mt-4">
                        <span class="text-xs uppercase text-gray-400">Administración</span>
                    </li>
                    <li>
                        <a href="{{ url(app()->getLocale() . '/core/users') }}" 
                           class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->is('*/core/users*') ? 'bg-gray-700' : '' }}">
                            👥 Usuarios
                        </a>
                    </li>
                    <li>
                        <a href="{{ url(app()->getLocale() . '/core/roles') }}" 
                           class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->is('*/core/roles*') ? 'bg-gray-700' : '' }}">
                            🔐 Roles
                        </a>
                    </li>
                    <li>
                        <a href="{{ url(app()->getLocale() . '/core/settings') }}" 
                           class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->is('*/core/settings*') ? 'bg-gray-700' : '' }}">
                            ⚙️ Configuración
                        </a>
                    </li>
                    @endcan
                </ul>
            </nav>
            <!-- Language Switcher -->
            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-700">
                <div class="flex justify-center space-x-2">
                    @foreach(supported_locales() as $locale => $info)
                        <a href="{{ current_route_multilang($locale) }}" 
                           class="px-3 py-1 rounded {{ app()->getLocale() === $locale ? 'bg-blue-600' : 'hover:bg-gray-700' }}">
                            {{ $info['flag'] ?? locale_flag($locale) }} {{ strtoupper($locale) }}
                        </a>
                    @endforeach
                </div>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="ml-64 p-8">
            @yield('content')
        </main>
    </div>
    @livewireScripts
</body>
</html>
