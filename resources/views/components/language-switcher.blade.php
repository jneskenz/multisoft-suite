@props(['class' => 'dropdown'])

{{-- <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle hide-arrow btn btn-text-{{ $moduleInfo['color'] ?? 'primary' }} rounded-pill px-2 px-md-3"
         href="javascript:void(0);" 
         data-bs-toggle="dropdown" 
         aria-expanded="false">
         <i class="icon-base ti tabler-language icon-22px text-heading me-0 me-md-1"></i>
         <span>{{ locale_flag(app()->getLocale()) }}</span>
         <span class="d-none d-sm-inline ms-1">{{ locale_name(app()->getLocale()) }}</span>
         <span class="d-none ms-2" id="nav-module-text"></span>
      </a>
      
      <ul class="dropdown-menu">
         @foreach(supported_locales() as $locale)
               <li>
                  <a 
                     class="dropdown-item d-flex align-items-center gap-2 {{ app()->getLocale() === $locale ? 'active' : '' }}" 
                     href="{{ current_route_multilang($locale) }}"
                  >
                     <span>{{ locale_flag($locale) }}</span>
                     <span>{{ locale_name($locale) }}</span>
                     @if(app()->getLocale() === $locale)
                           <i class="ti ti-check ms-auto text-success"></i>
                     @endif
                  </a>
               </li>
         @endforeach
      </ul>
</li> --}}
{{-- <li class="nav-item dropdown">
   
   <a class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-{{ $moduleInfo['color'] ?? 'primary' }} rounded-pill px-2 px-md-3"
      href="javascript:void(0);" 
      data-bs-toggle="dropdown" 
      aria-expanded="false">
      <i class="icon-base ti tabler-language icon-22px text-heading me-0 me-md-1"></i>
   </a>
   <ul class="dropdown-menu dropdown-menu-end">
      <li>
            <a class="dropdown-item {{ app()->getLocale() === 'es' ? 'active' : '' }}" href="{{ url('es' . '/' . request()->segment(2)) }}">
               <span class="fi fi-es fis rounded-circle me-2 fs-5"></span>
               <span class="align-middle">Español</span>
            </a>
      </li>
      <li>
            <a class="dropdown-item {{ app()->getLocale() === 'en' ? 'active' : '' }}" href="{{ url('en' . '/' . request()->segment(2)) }}">
               <span class="fi fi-us fis rounded-circle me-2 fs-5"></span>
               <span class="align-middle">English</span>
            </a>
      </li>
   </ul>
</li> --}}

@props(['class' => 'dropdown'])

<li class="nav-item dropdown-language dropdown">
      <a class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-{{ $moduleInfo['color'] ?? 'primary' }} rounded-pill"
         href="javascript:void(0);" 
         data-bs-toggle="dropdown" 
         aria-expanded="false">
         <i class="icon-base ti tabler-language icon-22px text-heading me-0 me-md-1"></i>
         {{-- <span>{{ locale_flag(app()->getLocale()) }}</span>
         <span class="d-none d-sm-inline ms-1">{{ locale_name(app()->getLocale()) }}</span>
         <span class="d-none ms-2" id="nav-module-text"></span> --}}
      </a>
      
      <ul class="dropdown-menu">
         @foreach(supported_locales() as $locale)
               <li>
                  <a 
                     class="dropdown-item d-flex align-items-center gap-2 {{ app()->getLocale() === $locale ? 'active' : '' }}" 
                     href="{{ current_route_multilang($locale) }}"
                  >  
                     {{-- <span class="fi fi-{{ locale_code($locale) }} fis rounded-circle me-2 fs-5"></span> --}}
                     <span>{{ locale_flag($locale) }}</span>
                     <span>{{ locale_name($locale) }}</span>
                     @if(app()->getLocale() === $locale)
                           <i class="ti tabler-check ms-auto text-success"></i>
                     @endif
                  </a>
               </li>
         @endforeach
      </ul>
</li>