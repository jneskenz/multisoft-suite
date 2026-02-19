@props(['class' => 'dropdown'])

@php
    $currentGroup = current_group();
    // $userGroups = auth()->check() ? auth()->user()->group_companies : collect();
    // $userGroups = auth()->check() ? collect(auth()->user()->group_companies) : collect();
    $userGroups = auth()->check() ? auth()->user()->groupCompanies : collect();
    $showSwitcher = $userGroups->count() > 1;
@endphp

@if($currentGroup)
<li class="nav-item dropdown-group {{ $class }}">
    @if($showSwitcher)
        {{-- Dropdown cuando hay múltiples grupos --}}
        <a class="nav-link dropdown-toggle hide-arrow btn btn-text-secondary rounded-pill d-flex align-items-center px-2 px-md-3"
           href="javascript:void(0);" 
           data-bs-toggle="dropdown" 
           aria-expanded="false">
           <span class="fi fi-{{ Str::lower($currentGroup->code) }} fis icon-22px rounded-circle"></span>
            {{-- <span class="fs-5">{{ $currentGroup->flag_emoji }}</span> --}}
            {{-- <span class="d-none d-md-inline ms-1">{{ $currentGroup->code }}</span> --}}
            {{-- <i class="ti tabler-chevron-down ti-xs ms-1 d-none d-md-inline"></i> --}}
        </a>
        
        <ul class="dropdown-menu dropdown-menu-start">
            <li>
                <h6 class="dropdown-header text-uppercase">{{ __('Cambiar Grupo') }}</h6>
            </li>
            <li><hr class="dropdown-divider"></li>
            @foreach($userGroups as $group)
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 {{ $currentGroup->id === $group->id ? 'active' : '' }}" 
                       href="{{ switch_group_url($group->code) }}">
                        {{-- <span class="fs-5">{{ $group->flag_emoji }}</span> --}}
                        <span class="fi fi-{{ Str::lower($group->code) }} fis icon-22px rounded-circle"></span>
                        <div class="d-flex flex-column">
                            <span class="fw-medium">{{ $group->code }}</span>
                            <small class="text-muted">{{ $group->display_name }}</small>
                        </div>
                        @if($currentGroup->id === $group->id)
                            <i class="ti tabler-check ms-auto text-success"></i>
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>
    @else
        {{-- Badge estático cuando solo hay un grupo --}}
        <span class="nav-link d-flex align-items-center gap-1 pe-none">
            {{-- <span class="fs-5">{{ $currentGroup->flag_emoji }}</span> --}}
            <span class="fi fi-{{ Str::lower($currentGroup->code) }} fis icon-22px rounded-circle"></span>
            {{-- <span class="badge bg-label-primary">{{ $currentGroup->code }}</span> --}}
            {{-- <span class="d-none d-md-inline ms-1">{{ $currentGroup->code }}</span> --}}

        </span>
    @endif
</li>
@endif
