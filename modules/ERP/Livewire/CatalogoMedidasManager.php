<?php

namespace Modules\ERP\Livewire;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\ERP\Models\CombinacionMedida;

class CatalogoMedidasManager extends Component
{
    public bool $showModal = false;

    /**
     * @var array{
     *   serie_visual: array<int,array<string,mixed>>,
     *   subserie_visual: array<int,array<string,mixed>>,
     *   medida_esferica: array<int,array<string,mixed>>,
     *   medida_cilindrica: array<int,array<string,mixed>>,
     *   adicion: array<int,array<string,mixed>>
     * }
     */
    public array $combinacionOptions = [
        'serie_visual' => [],
        'subserie_visual' => [],
        'medida_esferica' => [],
        'medida_cilindrica' => [],
        'adicion' => [],
    ];

    public int $catalogoId = 0;
    public int $categoriaId = 0;
    public string $categoriaCodigo = '';
    public string $categoriaNombre = '';
    public string $articuloCodigo = '';
    public string $articuloSubcategoria = '';
    public string $articuloDescripcion = '';

    public string $serieVisualId = '';
    public string $subserieVisualId = '';
    public string $medidaEsfericaDesdeId = '';
    public string $medidaEsfericaHastaId = '';
    public string $medidaCilindricaDesdeId = '';
    public string $medidaCilindricaHastaId = '';
    public string $adicionDesdeId = '';
    public string $adicionHastaId = '';

    public string $precioBase = '';
    public string $precioXMenorMinimo = '';
    public string $precioXMenorBase = '';
    public string $precioXMenorMaximo = '';
    public string $precioXMayorMinimo = '';
    public string $precioXMayorBase = '';
    public string $precioXMayorMaximo = '';

    public int $estado = 1;

    public function mount(array $combinacionOptions = []): void
    {
        $this->combinacionOptions = [
            'serie_visual' => $this->normalizarOpciones($combinacionOptions['serie_visual'] ?? []),
            'subserie_visual' => $this->normalizarOpciones($combinacionOptions['subserie_visual'] ?? []),
            'medida_esferica' => $this->normalizarOpciones($combinacionOptions['medida_esferica'] ?? []),
            'medida_cilindrica' => $this->normalizarOpciones($combinacionOptions['medida_cilindrica'] ?? []),
            'adicion' => $this->normalizarOpciones($combinacionOptions['adicion'] ?? []),
        ];
    }

    #[On('erp-open-medidas-modal')]
    public function abrirModalMedidas(
        int|string $catalogoId = 0,
        int|string $categoriaId = 0,
        string $categoriaCodigo = '',
        string $categoriaNombre = '',
        string $articuloCodigo = '',
        string $articuloSubcategoria = '',
        string $articuloDescripcion = ''
    ): void {
        $this->catalogoId = (int) $catalogoId;
        $this->categoriaId = (int) $categoriaId;
        $this->categoriaCodigo = strtoupper(trim($categoriaCodigo));
        $this->categoriaNombre = trim($categoriaNombre);
        $this->articuloCodigo = trim($articuloCodigo);
        $this->articuloSubcategoria = trim($articuloSubcategoria);
        $this->articuloDescripcion = trim($articuloDescripcion);

        $this->limpiarFormulario();
        $this->resetValidation();
        $this->showModal = true;
    }

    public function cerrarModal(): void
    {
        $this->showModal = false;
    }

    public function updatedSerieVisualId(): void
    {
        $actual = (int) $this->subserieVisualId;
        if ($actual <= 0) {
            return;
        }

        $permitidos = array_map(
            static fn($option) => (int) ($option['id'] ?? 0),
            $this->subseriesVisuales
        );

        if (!in_array($actual, $permitidos, true)) {
            $this->subserieVisualId = '';
        }
    }

    public function guardarCombinacion(): void
    {
        if ($this->catalogoId <= 0) {
            $this->dispatch('notify', type: 'warning', message: 'Seleccione un articulo para registrar combinaciones.');

            return;
        }

        if (!Schema::hasTable('erp_combinacion_medidas')) {
            $this->dispatch('notify', type: 'error', message: 'La tabla erp_combinacion_medidas no existe.');

            return;
        }

        $this->normalizarCamposDecimales();

        $validated = $this->validate(
            $this->reglas(),
            $this->mensajes()
        );

        if (!$this->validarSubserie()) {
            return;
        }

        if (!$this->validarAdicionEnCategoria('adicionDesdeId')) {
            return;
        }

        if (!$this->validarAdicionEnCategoria('adicionHastaId')) {
            return;
        }

        if (!$this->validarRango($this->medidasEsfericas, (int) $this->medidaEsfericaDesdeId, (int) $this->medidaEsfericaHastaId, 'medidaEsfericaHastaId', 'El rango de medida esferica no es valido.')) {
            return;
        }

        if (!$this->validarRango($this->medidasCilindricas, (int) $this->medidaCilindricaDesdeId, (int) $this->medidaCilindricaHastaId, 'medidaCilindricaHastaId', 'El rango de medida cilindrica no es valido.')) {
            return;
        }

        if (!$this->validarRango($this->adicionesDisponibles, (int) $this->adicionDesdeId, (int) $this->adicionHastaId, 'adicionHastaId', 'El rango de adicion no es valido.')) {
            return;
        }

        $payload = [
            'catalogo_id' => $this->catalogoId,
            'serie_visual_id' => (int) $validated['serieVisualId'],
            'subserie_visual_id' => (int) $validated['subserieVisualId'],
            'medida_esferica_desde_id' => (int) $validated['medidaEsfericaDesdeId'],
            'medida_esferica_hasta_id' => (int) $validated['medidaEsfericaHastaId'],
            'medida_cilindrica_desde_id' => (int) $validated['medidaCilindricaDesdeId'],
            'medida_cilindrica_hasta_id' => (int) $validated['medidaCilindricaHastaId'],
            'adicion_desde_id' => (int) $validated['adicionDesdeId'],
            'adicion_hasta_id' => (int) $validated['adicionHastaId'],
            'preciobase' => $this->decimalNormalizado($validated['precioBase']),
            'precio_x_menor_minimo' => $this->decimalNormalizado($validated['precioXMenorMinimo']),
            'precio_x_menor_base' => $this->decimalNormalizado($validated['precioXMenorBase']),
            'precio_x_menor_maximo' => $this->decimalNormalizado($validated['precioXMenorMaximo']),
            'precio_x_mayor_minimo' => $this->decimalNormalizado($validated['precioXMayorMinimo']),
            'precio_x_mayor_base' => $this->decimalNormalizado($validated['precioXMayorBase']),
            'precio_x_mayor_maximo' => $this->decimalNormalizado($validated['precioXMayorMaximo']),
            'estado' => $this->estado,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ];

        $yaExiste = CombinacionMedida::query()
            ->where('catalogo_id', $this->catalogoId)
            ->where('serie_visual_id', $payload['serie_visual_id'])
            ->where('subserie_visual_id', $payload['subserie_visual_id'])
            ->where('medida_esferica_desde_id', $payload['medida_esferica_desde_id'])
            ->where('medida_esferica_hasta_id', $payload['medida_esferica_hasta_id'])
            ->where('medida_cilindrica_desde_id', $payload['medida_cilindrica_desde_id'])
            ->where('medida_cilindrica_hasta_id', $payload['medida_cilindrica_hasta_id'])
            ->where('adicion_desde_id', $payload['adicion_desde_id'])
            ->where('adicion_hasta_id', $payload['adicion_hasta_id'])
            ->exists();

        if ($yaExiste) {
            $this->addError('serieVisualId', 'La combinacion seleccionada ya existe para este articulo.');

            return;
        }

        try {
            CombinacionMedida::query()->create($payload);
            $this->dispatch('notify', type: 'success', message: 'Combinacion registrada correctamente.');
            $this->limpiarFormulario();
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('notify', type: 'error', message: 'No se pudo registrar la combinacion.');
        }
    }

    public function eliminarCombinacion(int|string $combinacionId): void
    {
        $id = (int) $combinacionId;
        if ($id <= 0 || $this->catalogoId <= 0) {
            return;
        }

        $registro = CombinacionMedida::query()
            ->whereKey($id)
            ->where('catalogo_id', $this->catalogoId)
            ->first();

        if (!$registro) {
            $this->dispatch('notify', type: 'warning', message: 'No se encontro la combinacion a eliminar.');

            return;
        }

        try {
            $registro->updated_by = auth()->id();
            $registro->save();
            $registro->delete();
            $this->dispatch('notify', type: 'success', message: 'Combinacion eliminada correctamente.');
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('notify', type: 'error', message: 'No se pudo eliminar la combinacion.');
        }
    }

    public function limpiarFormulario(): void
    {
        $this->serieVisualId = '';
        $this->subserieVisualId = '';
        $this->medidaEsfericaDesdeId = '';
        $this->medidaEsfericaHastaId = '';
        $this->medidaCilindricaDesdeId = '';
        $this->medidaCilindricaHastaId = '';
        $this->adicionDesdeId = '';
        $this->adicionHastaId = '';
        $this->precioBase = '';
        $this->precioXMenorMinimo = '';
        $this->precioXMenorBase = '';
        $this->precioXMenorMaximo = '';
        $this->precioXMayorMinimo = '';
        $this->precioXMayorBase = '';
        $this->precioXMayorMaximo = '';
        $this->estado = 1;
        $this->resetValidation();
    }

    #[Computed]
    public function seriesVisuales(): array
    {
        return $this->combinacionOptions['serie_visual'] ?? [];
    }

    #[Computed]
    public function subseriesVisuales(): array
    {
        $items = $this->combinacionOptions['subserie_visual'] ?? [];
        $serieId = (int) $this->serieVisualId;
        if ($serieId <= 0) {
            return $items;
        }

        $filtered = array_values(array_filter($items, static function (array $item) use ($serieId): bool {
            $itemSerieId = (int) ($item['serie_visual_id'] ?? 0);

            return $itemSerieId === 0 || $itemSerieId === $serieId;
        }));

        return $filtered !== [] ? $filtered : $items;
    }

    #[Computed]
    public function medidasEsfericas(): array
    {
        return $this->combinacionOptions['medida_esferica'] ?? [];
    }

    #[Computed]
    public function medidasCilindricas(): array
    {
        return $this->combinacionOptions['medida_cilindrica'] ?? [];
    }

    #[Computed]
    public function adicionesDisponibles(): array
    {
        $items = $this->combinacionOptions['adicion'] ?? [];
        if ($this->categoriaId <= 0) {
            return $items;
        }

        $filtered = array_values(array_filter($items, function (array $item): bool {
            $itemCategoriaId = (int) ($item['categoria_id'] ?? 0);

            return $itemCategoriaId === 0 || $itemCategoriaId === $this->categoriaId;
        }));

        return $filtered !== [] ? $filtered : $items;
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    #[Computed]
    public function combinaciones(): array
    {
        if ($this->catalogoId <= 0 || !Schema::hasTable('erp_combinacion_medidas')) {
            return [];
        }

        return DB::table('erp_combinacion_medidas as cm')
            ->leftJoin('erp_serie_visual as sv', 'sv.id', '=', 'cm.serie_visual_id')
            ->leftJoin('erp_subserie_visual as ssv', 'ssv.id', '=', 'cm.subserie_visual_id')
            ->leftJoin('erp_medida_esferica as med', 'med.id', '=', 'cm.medida_esferica_desde_id')
            ->leftJoin('erp_medida_esferica as meh', 'meh.id', '=', 'cm.medida_esferica_hasta_id')
            ->leftJoin('erp_medida_cilindrica as mcd', 'mcd.id', '=', 'cm.medida_cilindrica_desde_id')
            ->leftJoin('erp_medida_cilindrica as mch', 'mch.id', '=', 'cm.medida_cilindrica_hasta_id')
            ->leftJoin('erp_adiciones as ad', 'ad.id', '=', 'cm.adicion_desde_id')
            ->leftJoin('erp_adiciones as ah', 'ah.id', '=', 'cm.adicion_hasta_id')
            ->select([
                'cm.id',
                'cm.estado',
                'sv.nombre as serie_visual',
                'ssv.nombre as subserie_visual',
                'med.nombre as medida_esferica_desde',
                'meh.nombre as medida_esferica_hasta',
                'mcd.nombre as medida_cilindrica_desde',
                'mch.nombre as medida_cilindrica_hasta',
                'ad.nombre as adicion_desde',
                'ah.nombre as adicion_hasta',
                'cm.preciobase',
                'cm.precio_x_menor_minimo',
                'cm.precio_x_menor_base',
                'cm.precio_x_menor_maximo',
                'cm.precio_x_mayor_minimo',
                'cm.precio_x_mayor_base',
                'cm.precio_x_mayor_maximo',
            ])
            ->where('cm.catalogo_id', $this->catalogoId)
            ->whereNull('cm.deleted_at')
            ->orderByDesc('cm.id')
            ->limit(100)
            ->get()
            ->map(function ($item): array {
                return [
                    'id' => (int) $item->id,
                    'estado' => (int) $item->estado,
                    'serie_visual' => (string) ($item->serie_visual ?? '-'),
                    'subserie_visual' => (string) ($item->subserie_visual ?? '-'),
                    'medida_esferica_desde' => (string) ($item->medida_esferica_desde ?? '-'),
                    'medida_esferica_hasta' => (string) ($item->medida_esferica_hasta ?? '-'),
                    'medida_cilindrica_desde' => (string) ($item->medida_cilindrica_desde ?? '-'),
                    'medida_cilindrica_hasta' => (string) ($item->medida_cilindrica_hasta ?? '-'),
                    'adicion_desde' => (string) ($item->adicion_desde ?? '-'),
                    'adicion_hasta' => (string) ($item->adicion_hasta ?? '-'),
                    'preciobase' => $this->decimalConFormato((string) $item->preciobase),
                    'precio_x_menor_minimo' => $this->decimalConFormato((string) $item->precio_x_menor_minimo),
                    'precio_x_menor_base' => $this->decimalConFormato((string) $item->precio_x_menor_base),
                    'precio_x_menor_maximo' => $this->decimalConFormato((string) $item->precio_x_menor_maximo),
                    'precio_x_mayor_minimo' => $this->decimalConFormato((string) $item->precio_x_mayor_minimo),
                    'precio_x_mayor_base' => $this->decimalConFormato((string) $item->precio_x_mayor_base),
                    'precio_x_mayor_maximo' => $this->decimalConFormato((string) $item->precio_x_mayor_maximo),
                ];
            })
            ->values()
            ->all();
    }

    public function etiquetaOpcion(array $option): string
    {
        $nombre = trim((string) ($option['nombre'] ?? ''));
        $codigo = trim((string) ($option['codigo'] ?? ''));

        if ($nombre === '' && $codigo === '') {
            return '-';
        }

        if ($nombre === '' || strcasecmp($nombre, $codigo) === 0) {
            return $codigo !== '' ? $codigo : $nombre;
        }

        if ($codigo === '') {
            return $nombre;
        }

        return $nombre . ' (' . $codigo . ')';
    }

    public function render()
    {
        return view('erp::livewire.catalogo-medidas-manager');
    }

    /**
     * @return array<string,array<int,string>>
     */
    private function reglas(): array
    {
        return [
            'serieVisualId' => ['required', 'integer', 'exists:erp_serie_visual,id'],
            'subserieVisualId' => ['required', 'integer', 'exists:erp_subserie_visual,id'],
            'medidaEsfericaDesdeId' => ['required', 'integer', 'exists:erp_medida_esferica,id'],
            'medidaEsfericaHastaId' => ['required', 'integer', 'exists:erp_medida_esferica,id'],
            'medidaCilindricaDesdeId' => ['required', 'integer', 'exists:erp_medida_cilindrica,id'],
            'medidaCilindricaHastaId' => ['required', 'integer', 'exists:erp_medida_cilindrica,id'],
            'adicionDesdeId' => ['required', 'integer', 'exists:erp_adiciones,id'],
            'adicionHastaId' => ['required', 'integer', 'exists:erp_adiciones,id'],
            'precioBase' => ['required', 'numeric', 'min:0'],
            'precioXMenorMinimo' => ['required', 'numeric', 'min:0'],
            'precioXMenorBase' => ['required', 'numeric', 'min:0'],
            'precioXMenorMaximo' => ['required', 'numeric', 'min:0'],
            'precioXMayorMinimo' => ['required', 'numeric', 'min:0'],
            'precioXMayorBase' => ['required', 'numeric', 'min:0'],
            'precioXMayorMaximo' => ['required', 'numeric', 'min:0'],
        ];
    }

    /**
     * @return array<string,string>
     */
    private function mensajes(): array
    {
        return [
            'serieVisualId.required' => 'La serie visual es obligatoria.',
            'subserieVisualId.required' => 'La subserie visual es obligatoria.',
            'medidaEsfericaDesdeId.required' => 'La medida esferica desde es obligatoria.',
            'medidaEsfericaHastaId.required' => 'La medida esferica hasta es obligatoria.',
            'medidaCilindricaDesdeId.required' => 'La medida cilindrica desde es obligatoria.',
            'medidaCilindricaHastaId.required' => 'La medida cilindrica hasta es obligatoria.',
            'adicionDesdeId.required' => 'La adicion desde es obligatoria.',
            'adicionHastaId.required' => 'La adicion hasta es obligatoria.',
            'precioBase.required' => 'El precio costo es obligatorio.',
            'precioXMenorMinimo.required' => 'El precio por menor minimo es obligatorio.',
            'precioXMenorBase.required' => 'El precio por menor base es obligatorio.',
            'precioXMenorMaximo.required' => 'El precio por menor maximo es obligatorio.',
            'precioXMayorMinimo.required' => 'El precio por mayor minimo es obligatorio.',
            'precioXMayorBase.required' => 'El precio por mayor base es obligatorio.',
            'precioXMayorMaximo.required' => 'El precio por mayor maximo es obligatorio.',
            'precioBase.numeric' => 'El precio costo debe ser numerico.',
            'precioXMenorMinimo.numeric' => 'El precio por menor minimo debe ser numerico.',
            'precioXMenorBase.numeric' => 'El precio por menor base debe ser numerico.',
            'precioXMenorMaximo.numeric' => 'El precio por menor maximo debe ser numerico.',
            'precioXMayorMinimo.numeric' => 'El precio por mayor minimo debe ser numerico.',
            'precioXMayorBase.numeric' => 'El precio por mayor base debe ser numerico.',
            'precioXMayorMaximo.numeric' => 'El precio por mayor maximo debe ser numerico.',
        ];
    }

    /**
     * @param array<int,array<string,mixed>> $options
     * @return array<int,array<string,mixed>>
     */
    private function normalizarOpciones(array $options): array
    {
        return array_values(array_map(
            static fn(array $option) => [
                'id' => (int) ($option['id'] ?? 0),
                'nombre' => (string) ($option['nombre'] ?? ''),
                'codigo' => isset($option['codigo']) ? (string) $option['codigo'] : null,
                'serie_visual_id' => isset($option['serie_visual_id']) ? (int) $option['serie_visual_id'] : null,
                'categoria_id' => isset($option['categoria_id']) ? (int) $option['categoria_id'] : null,
            ],
            array_filter($options, static fn($option) => is_array($option))
        ));
    }

    private function validarSubserie(): bool
    {
        $subserieId = (int) $this->subserieVisualId;
        if ($subserieId <= 0) {
            return true;
        }

        $serieId = (int) $this->serieVisualId;
        foreach ($this->subseriesVisuales as $option) {
            if ((int) ($option['id'] ?? 0) !== $subserieId) {
                continue;
            }

            $optionSerieId = (int) ($option['serie_visual_id'] ?? 0);
            if ($optionSerieId !== 0 && $serieId > 0 && $optionSerieId !== $serieId) {
                $this->addError('subserieVisualId', 'La subserie visual no corresponde a la serie seleccionada.');

                return false;
            }

            return true;
        }

        $this->addError('subserieVisualId', 'Seleccione una subserie visual valida.');

        return false;
    }

    private function validarAdicionEnCategoria(string $campo): bool
    {
        $id = (int) $this->{$campo};
        if ($id <= 0) {
            return true;
        }

        foreach ($this->adicionesDisponibles as $option) {
            if ((int) ($option['id'] ?? 0) === $id) {
                return true;
            }
        }

        $this->addError($campo, 'La adicion seleccionada no corresponde con la categoria actual.');

        return false;
    }

    /**
     * @param array<int,array<string,mixed>> $options
     */
    private function validarRango(array $options, int $desdeId, int $hastaId, string $campoError, string $mensaje): bool
    {
        if ($desdeId <= 0 || $hastaId <= 0) {
            return true;
        }

        $desde = $this->valorNumerico($options, $desdeId);
        $hasta = $this->valorNumerico($options, $hastaId);
        if ($desde === null || $hasta === null) {
            return true;
        }

        if ($desde > $hasta) {
            $this->addError($campoError, $mensaje);

            return false;
        }

        return true;
    }

    /**
     * @param array<int,array<string,mixed>> $options
     */
    private function valorNumerico(array $options, int $targetId): ?float
    {
        foreach ($options as $option) {
            if ((int) ($option['id'] ?? 0) !== $targetId) {
                continue;
            }

            $raw = trim((string) ($option['codigo'] ?? $option['nombre'] ?? ''));
            if ($raw === '') {
                return null;
            }

            $raw = str_replace(',', '.', $raw);
            if (preg_match('/[-+]?\d+(?:\.\d+)?/', $raw, $matches) !== 1) {
                return null;
            }

            return (float) $matches[0];
        }

        return null;
    }

    private function normalizarCamposDecimales(): void
    {
        $this->precioBase = $this->decimalNormalizado($this->precioBase);
        $this->precioXMenorMinimo = $this->decimalNormalizado($this->precioXMenorMinimo);
        $this->precioXMenorBase = $this->decimalNormalizado($this->precioXMenorBase);
        $this->precioXMenorMaximo = $this->decimalNormalizado($this->precioXMenorMaximo);
        $this->precioXMayorMinimo = $this->decimalNormalizado($this->precioXMayorMinimo);
        $this->precioXMayorBase = $this->decimalNormalizado($this->precioXMayorBase);
        $this->precioXMayorMaximo = $this->decimalNormalizado($this->precioXMayorMaximo);
    }

    private function decimalNormalizado(mixed $value): string
    {
        $raw = trim((string) $value);
        if ($raw === '') {
            return '';
        }

        $raw = str_replace(',', '.', $raw);

        return number_format((float) $raw, 2, '.', '');
    }

    private function decimalConFormato(string $value): string
    {
        if (trim($value) === '') {
            return '0.00';
        }

        return number_format((float) $value, 2, '.', '');
    }
}
