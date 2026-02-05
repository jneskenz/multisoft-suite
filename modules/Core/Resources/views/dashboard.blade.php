@extends('layouts.app')

@section('title', __('Dashboard'))

@section('content')
<div class="space-y-6">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
        {{ __('Dashboard') }}
    </h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Card: Partners -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                    <span class="text-2xl">👥</span>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Partners</h2>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">0</p>
                </div>
            </div>
        </div>

        <!-- Card: ERP -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                    <span class="text-2xl">🏢</span>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Productos</h2>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">0</p>
                </div>
            </div>
        </div>

        <!-- Card: FMS -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-full">
                    <span class="text-2xl">💰</span>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Balance') }}</h2>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">$0.00</p>
                </div>
            </div>
        </div>

        <!-- Card: HR -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-full">
                    <span class="text-2xl">👤</span>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Empleados') }}</h2>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">0</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Welcome Message -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
            {{ __('Bienvenido a Multisoft Suite') }}
        </h2>
        <p class="text-gray-600 dark:text-gray-300">
            {{ __('Suite empresarial completa con módulos integrados de ERP, CRM, RRHH y Gestión Financiera.') }}
        </p>
        <div class="mt-4 flex flex-wrap gap-2">
            <span class="px-3 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full text-sm">
                Laravel 12
            </span>
            <span class="px-3 py-1 bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 rounded-full text-sm">
                Livewire 4
            </span>
            <span class="px-3 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-full text-sm">
                PostgreSQL
            </span>
            <span class="px-3 py-1 bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200 rounded-full text-sm">
                Modular Architecture
            </span>
        </div>
    </div>
</div>
@endsection
