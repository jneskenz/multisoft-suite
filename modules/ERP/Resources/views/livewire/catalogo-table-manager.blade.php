<div>
    @include('erp::catalogos.partials.catalogos-table', [
        'rows' => $rows,
        'categoriaId' => $categoriaId,
        'categoriaNombre' => $categoriaNombre,
        'categoriaCodigo' => $categoriaCodigo,
        'categoriasConMedidas' => $categoriasConMedidas,
    ])
</div>

