<!-- breadcrumbs|start -->
{{-- como optener esos datos aqui  --}}
<div
    class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 row-gap-4">
    <div class="d-flex flex-row justify-content-center align-items-center">
        {{-- <button type="button" class="btn btn-label-secondary rounded px-1 px-md-2  me-1 me-md-3 btn-mobile-sm waves-effect" --}}
        <button type="button" class="btn btn-label-secondary rounded d-flex align-items-center px-1 px-md-2 me-1 me-md-3 btn-mobile-sm waves-effect"
            title="{{ $items['description'] }}">
            <i class=" {{ $items['icon'] }}"></i>
        </button>
        <div>
            <h6 class="mb-1 d-none d-md-inline">
                <span style="padding-top:2px">{{ $items['title'] }}</span>
            </h6>
            <div class="page-title-right" style="opacity: 0.7;">
                <ol class="breadcrumb m-0">
                    @foreach ($items['items'] as $item)
                        <li class="breadcrumb-item {{ $loop->last ? '' : 'active' }}">
                            <a class="breadcrumb-link" href="{{ $item['url'] ?? '#' }}">{{ $item['name'] }}</a>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </div>
    <div class="d-flex align-content-center flex-wrap gap-4">
        {{ $extra ?? '' }}
        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            {{ $acciones ?? '' }}
        </div>
    </div>
</div>
<!-- breadcrumbs|end -->
