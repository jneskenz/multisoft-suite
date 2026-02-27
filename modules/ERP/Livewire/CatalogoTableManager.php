<?php

namespace Modules\ERP\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;
use Modules\ERP\Services\CatalogoService;

class CatalogoTableManager extends Component
{
    public int $categoriaId = 0;
    public string $categoriaNombre = '';
    public string $categoriaCodigo = '';

    /**
     * @var array<int,string>
     */
    public array $categoriasConMedidas = [];

    public function mount(
        int|string $categoriaId = 0,
        string $categoriaNombre = '',
        string $categoriaCodigo = '',
        array $categoriasConMedidas = []
    ): void {
        $this->categoriaId = (int) $categoriaId;
        $this->categoriaNombre = trim($categoriaNombre);
        $this->categoriaCodigo = strtoupper(trim($categoriaCodigo));
        $this->categoriasConMedidas = array_values(array_map(
            static fn($item) => strtoupper(trim((string) $item)),
            $categoriasConMedidas
        ));
    }

    #[On('erp-catalogo-saved')]
    public function cuandoCatalogoGuardado(mixed $categoriaId = 0): void
    {
        $this->refrescarSiCorresponde($this->resolverCategoriaIdEvento($categoriaId));
    }

    #[On('erp-catalogo-deleted')]
    public function cuandoCatalogoEliminado(mixed $categoriaId = 0): void
    {
        $this->refrescarSiCorresponde($this->resolverCategoriaIdEvento($categoriaId));
    }

    public function render()
    {
        /** @var CatalogoService $catalogoService */
        $catalogoService = app(CatalogoService::class);
        $rows = $catalogoService->listByCategoria($this->categoriaId);

        return view('erp::livewire.catalogo-table-manager', [
            'rows' => $rows,
            'categoriaId' => $this->categoriaId,
            'categoriaNombre' => $this->categoriaNombre,
            'categoriaCodigo' => $this->categoriaCodigo,
            'categoriasConMedidas' => $this->categoriasConMedidas,
        ]);
    }

    private function refrescarSiCorresponde(int $categoriaId): void
    {
        if ($categoriaId > 0 && $categoriaId !== $this->categoriaId) {
            $this->skipRender();
        }
    }

    private function resolverCategoriaIdEvento(mixed $categoriaId): int
    {
        if (is_array($categoriaId)) {
            if (array_key_exists('categoriaId', $categoriaId)) {
                return (int) $categoriaId['categoriaId'];
            }

            if (array_key_exists(0, $categoriaId) && is_array($categoriaId[0]) && array_key_exists('categoriaId', $categoriaId[0])) {
                return (int) $categoriaId[0]['categoriaId'];
            }
        }

        if (is_object($categoriaId) && isset($categoriaId->categoriaId)) {
            return (int) $categoriaId->categoriaId;
        }

        return (int) $categoriaId;
    }
}
