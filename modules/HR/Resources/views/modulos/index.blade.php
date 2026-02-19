@extends('layouts.app')

@php
    $locale = app()->getLocale();
    $group = request()->route('group') ?? current_group_code() ?? 'PE';

    $sectionTitle = is_array($seccionMenu['title'] ?? null)
        ? ($seccionMenu['title'][$locale] ?? ($seccionMenu['title']['en'] ?? ($seccionMenu['key'] ?? '')))
        : ($seccionMenu['title'] ?? ($seccionMenu['key'] ?? ''));
@endphp

@section('title', __('RRHH - :section', ['section' => $sectionTitle]))

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-6">
        <div>
            <h4 class="mb-1 fw-bold">{{ $sectionTitle }}</h4>
            <p class="text-muted mb-0">{{ __('Seleccione una opcion del submenu.') }}</p>
        </div>
        <a href="{{ route('hr.index', ['locale' => $locale, 'group' => $group]) }}" class="btn btn-outline-primary">
            <i class="ti tabler-arrow-left me-1"></i>
            {{ __('Volver al dashboard HR') }}
        </a>
    </div>

    <div class="row g-4">
        @foreach (($seccionMenu['children'] ?? []) as $menuGroup)
            @php
                $groupTitle = is_array($menuGroup['title'] ?? null)
                    ? ($menuGroup['title'][$locale] ?? ($menuGroup['title']['en'] ?? ($menuGroup['key'] ?? '')))
                    : ($menuGroup['title'] ?? ($menuGroup['key'] ?? ''));

                $groupIcon = $menuGroup['icon'] ?? 'ti tabler-list';
                $groupLinks = $menuGroup['children'] ?? [];
            @endphp

            <div class="col-12 col-lg-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="d-flex align-items-center mb-3">
                            <i class="{{ $groupIcon }} me-2"></i>
                            {{ $groupTitle }}
                        </h5>

                        <div class="list-group list-group-flush">
                            @forelse ($groupLinks as $link)
                                @php
                                    $linkTitle = is_array($link['title'] ?? null)
                                        ? ($link['title'][$locale] ?? ($link['title']['en'] ?? ($link['key'] ?? '')))
                                        : ($link['title'] ?? ($link['key'] ?? ''));

                                    $linkIcon = $link['icon'] ?? 'ti tabler-point';
                                    $linkRoute = $link['route'] ?? null;
                                @endphp

                                @if ($linkRoute && Route::has($linkRoute))
                                    <a href="{{ route($linkRoute, ['locale' => $locale, 'group' => $group]) }}"
                                        class="list-group-item list-group-item-action d-flex align-items-center">
                                        <i class="{{ $linkIcon }} me-2"></i>
                                        <span>{{ $linkTitle }}</span>
                                    </a>
                                @else
                                    <div class="list-group-item d-flex align-items-center text-muted">
                                        <i class="{{ $linkIcon }} me-2"></i>
                                        <span>{{ $linkTitle }}</span>
                                        <span class="badge bg-label-secondary ms-auto">{{ __('Proximamente') }}</span>
                                    </div>
                                @endif
                            @empty
                                <div class="list-group-item text-muted">{{ __('Sin opciones disponibles.') }}</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
