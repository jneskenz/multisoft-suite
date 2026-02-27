<?php

namespace Modules\ERP\Livewire;

use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\ERP\Services\CatalogoFieldMap;
use Modules\ERP\Services\CatalogoService;

class CatalogoModalManager extends Component
{
    use WithFileUploads;

    private const FIELD_DEPENDENCIES = [
        'detallecolor' => [
            'parent' => 'color',
            'column' => 'color_id',
        ],
    ];

    private const FIELD_LABELS = [
        'subcategoria' => 'Subcategoria',
        'material' => 'Material',
        'marca' => 'Marca',
        'tipo' => 'Tipo',
        'talla' => 'Talla',
        'color' => 'Color',
        'detallecolor' => 'Detalle de color',
        'clase' => 'Clase',
        'genero' => 'Genero',
        'presentacion' => 'Presentacion',
        'imagen' => 'Imagen',
        'fotocromatico' => 'Fotocromatico',
        'tratamiento' => 'Tratamiento',
        'indice' => 'Indice',
        'ojobifocal' => 'Ojo bifocal',
        'adicion' => 'Adicion',
        'modalidad' => 'Modalidad',
        'cb' => 'CB',
        'o' => 'O',
        'colorluna' => 'Color de luna',
        'modelo' => 'Modelo',
        'base' => 'Base',
        'medida' => 'Medida',
        'diametro' => 'Diametro',
    ];

    private const FIELD_TABLES = [
        'subcategoria' => 'erp_subcategorias',
        'material' => 'erp_material',
        'marca' => 'erp_marcas',
        'tipo' => 'erp_tipos',
        'talla' => 'erp_tallas',
        'color' => 'erp_colores',
        'colorluna' => 'erp_colores',
        'detallecolor' => 'erp_detalle_colores',
        'clase' => 'erp_clases',
        'genero' => 'erp_generos',
        'presentacion' => 'erp_unidad_medidas',
        'fotocromatico' => 'erp_fotocromaticos',
        'tratamiento' => 'erp_tratamientos',
        'indice' => 'erp_indices',
        'ojobifocal' => 'erp_ojobifocales',
        'adicion' => 'erp_adiciones',
        'modalidad' => 'erp_modalidades',
        'cb' => 'erp_cb',
        'o' => 'erp_o',
        'modelo' => 'erp_modelos',
        'base' => 'erp_poderes',
        'medida' => 'erp_unidad_medidas',
        'diametro' => 'erp_diametros',
    ];

    private const CATEGORY_SCOPED_FIELDS = [
        'subcategoria',
        'material',
        'tipo',
        'marca',
        'talla',
        'color',
        'colorluna',
        'clase',
        'fotocromatico',
        'tratamiento',
        'indice',
        'ojobifocal',
        'diametro',
        'adicion',
        'modelo',
    ];

    public bool $showModal = false;
    public bool $restoreMainModalAfterCrud = false;
    public ?int $registroId = null;
    public int $categoriaId = 0;
    public int $categoriaSlot = 0;
    public string $categoriaNombre = '';
    public string $table = 'erp_catalogos';

    public string $subcategoria = '';
    public string $codigo = '';
    public string $descripcion = '';
    public int $estado = 1;
    public mixed $imagenUpload = null;

    public bool $showFieldCrudModal = false;
    public string $crudField = '';
    public string $crudTable = '';
    public string $crudCodigo = '';
    public string $crudNombre = '';
    public int $crudEstado = 1;
    public ?int $crudRecordId = null;
    public array $crudRows = [];
    public string $crudRelationLabel = '';
    public ?string $crudRelationColumn = null;
    public ?int $crudRelationValue = null;
    public string $crudRelationName = '';
    public string $crudRelationMessage = '';
    public bool $crudHasCodigo = true;
    public bool $crudHasEstado = true;

    /**
     * Valores de campos dinamicos (selects por categoria).
     *
     * @var array<string,string>
     */
    public array $values = [];

    /**
     * Opciones por categoria y campo.
     *
     * @var array<string,array<string,array<int,array{id:mixed,nombre:string,codigo:?string}>>>
     */
    public array $catalogoOptions = [];

    /**
     * Metadata por categoria.
     *
     * @var array<string,array{codigo?:string,nombre?:string,caracteristicas?:array<int,string>,campos_autocompletado?:array<int,string>}>
     */
    public array $catalogoCategoryMeta = [];

    /**
     * Slot de tab por categoria.
     *
     * @var array<string,int>
     */
    public array $categoriaSlotById = [];

    public function mount(
        array $catalogoOptions = [],
        array $catalogoCategoryMeta = [],
        array $categoriaSlotById = []
    ): void {
        $this->catalogoOptions = $catalogoOptions;
        $this->catalogoCategoryMeta = $catalogoCategoryMeta;
        $this->categoriaSlotById = $categoriaSlotById;
    }

    #[Computed]
    public function categoriaMeta(): array
    {
        return $this->catalogoCategoryMeta[(string) $this->categoriaId] ?? [];
    }

    #[Computed]
    public function optionsForCategoria(): array
    {
        return $this->catalogoOptions[(string) $this->categoriaId] ?? [];
    }

    /**
     * @return array<int,string>
     */
    #[Computed]
    public function dynamicFields(): array
    {
        $meta = $this->categoriaMeta();
        $fields = $meta['caracteristicas'] ?? [];

        if (!is_array($fields)) {
            return [];
        }

        return array_values(array_filter(array_map(
            fn($item) => is_string($item) ? trim($item) : '',
            $fields
        )));
    }

    /**
     * @return array<int,string>
     */
    #[Computed]
    public function autocompleteFields(): array
    {
        $meta = $this->categoriaMeta();
        $fields = $meta['campos_autocompletado'] ?? [];

        if (!is_array($fields)) {
            return [];
        }

        return array_values(array_filter(array_map(
            fn($item) => is_string($item) ? trim($item) : '',
            $fields
        )));
    }

    /**
     * @return array<int,array{id:mixed,nombre:string,codigo:?string}>
     */
    public function fieldOptions(string $field): array
    {
        $optionsByCategoria = $this->optionsForCategoria();
        $options = $optionsByCategoria[$field] ?? [];

        if (!is_array($options)) {
            return [];
        }

        $dependency = self::FIELD_DEPENDENCIES[$field] ?? null;
        if (!$dependency) {
            return $options;
        }

        $parentField = (string) ($dependency['parent'] ?? '');
        $parentColumn = (string) ($dependency['column'] ?? '');
        if ($parentField === '' || $parentColumn === '') {
            return $options;
        }

        $parentValue = (string) ($this->values[$parentField] ?? '');
        if ($parentValue === '') {
            return [];
        }

        return array_values(array_filter($options, function (array $option) use ($parentColumn, $parentValue): bool {
            return (string) ($option[$parentColumn] ?? '') === $parentValue;
        }));
    }

    public function isFieldDisabled(string $field): bool
    {
        $dependency = self::FIELD_DEPENDENCIES[$field] ?? null;
        if (!$dependency) {
            return false;
        }

        $parentField = (string) ($dependency['parent'] ?? '');
        if ($parentField === '') {
            return false;
        }

        return (string) ($this->values[$parentField] ?? '') === '';
    }

    private function syncDependentFields(string $changedField): void
    {
        foreach (self::FIELD_DEPENDENCIES as $childField => $dependency) {
            if (($dependency['parent'] ?? '') !== $changedField) {
                continue;
            }

            $selectedChildValue = (string) ($this->values[$childField] ?? '');
            if ($selectedChildValue === '') {
                continue;
            }

            $valid = false;
            foreach ($this->fieldOptions($childField) as $option) {
                if ((string) ($option['id'] ?? '') === $selectedChildValue) {
                    $valid = true;
                    break;
                }
            }

            if (!$valid) {
                $this->values[$childField] = '';
            }
        }
    }

    public function updatedSubcategoria(mixed $value = null): void
    {
        $this->rebuildDescripcion();
    }

    public function updatedCodigo(mixed $value = null): void
    {
        $this->rebuildDescripcion();
    }

    public function updatedValues(mixed $value = null, ?string $key = null): void
    {
        if (is_string($key) && $key !== '') {
            $this->syncDependentFields($key);
        }

        $this->rebuildDescripcion();
    }

    public function updatedImagenUpload(mixed $value = null): void
    {
        // Mantiene un valor consistente mientras no se persista aun en BD.
        $this->values['imagen'] = '';
    }

    #[Computed]
    public function barcodePayload(): string
    {
        $payload = trim($this->descripcion);
        if ($payload === '') {
            return 'SIN-DESCRIPCION';
        }

        $payload = preg_replace('/\s+/', ' ', $payload) ?? $payload;

        return mb_substr($payload, 0, 80);
    }

    #[Computed]
    public function barcodeUrl(): string
    {
        $text = rawurlencode($this->barcodePayload());
        $barcodeScript = public_path('lib/phpbarcode/barcode.php');

        if (is_file($barcodeScript)) {
            return url("/lib/phpbarcode/barcode.php?text={$text}&size=60&orientation=horizontal&codetype=code128&print=true&sizefactor=2");
        }

        return $this->fallbackBarcodeDataUri('barcode.php no encontrado en /public/lib/phpbarcode');
    }

    #[Computed]
    public function previewImageUrl(): string
    {
        if ($this->imagenUpload && method_exists($this->imagenUpload, 'temporaryUrl')) {
            try {
                return $this->imagenUpload->temporaryUrl();
            } catch (\Throwable) {
                // Si falla el temporal, usa imagen base.
            }
        }

        $selected = (string) ($this->values['imagen'] ?? '');
        if ($selected === '') {
            $selected = 'articulo_default.png';
        }

        if (
            str_starts_with($selected, 'http://')
            || str_starts_with($selected, 'https://')
            || str_starts_with($selected, '/')
            || str_starts_with($selected, 'data:')
        ) {
            return $selected;
        }

        return asset('src/img/articulos/' . ltrim($selected, '/'));
    }

    #[On('erp-open-catalogo-modal')]
    public function openModal(
        int|string $categoriaId = 0,
        int|string $categoriaSlot = 0,
        string $categoriaNombre = '',
        string $categoriaTabla = 'erp_catalogos',
        int|string $registroId = 0,
        int|string $duplicar = 0
    ): void {
        $this->resetFormState();

        $this->categoriaId = (int) $categoriaId;
        $resolvedSlot = (int) $categoriaSlot;
        if ($resolvedSlot <= 0) {
            $resolvedSlot = (int) ($this->categoriaSlotById[(string) $this->categoriaId] ?? 0);
        }
        $this->categoriaSlot = $resolvedSlot;
        $meta = $this->categoriaMeta();
        $this->categoriaNombre = trim($categoriaNombre) !== ''
            ? $categoriaNombre
            : (string) ($meta['nombre'] ?? 'CATEGORIA');
        $this->table = trim($categoriaTabla) !== '' ? $categoriaTabla : 'erp_catalogos';
        $this->initializeDynamicValues();
        $requestedRegistroId = (int) $registroId;
        $duplicateMode = (int) $duplicar > 0;
        if ($requestedRegistroId > 0) {
            $this->loadCatalogoForEdit($requestedRegistroId);
            if ($duplicateMode) {
                $this->prepareDuplicateFromLoadedRecord();
            }
        }
        $this->showModal = true;
        if ($duplicateMode) {
            $this->rebuildDescripcion();
            $this->dispatch('notify', type: 'info', message: 'Registro duplicado en modo nuevo. Completa codigo y guarda.');
        } elseif ($requestedRegistroId > 0) {
            if (trim($this->descripcion) === '') {
                $this->rebuildDescripcion();
            }
        } else {
            $this->rebuildDescripcion();
        }
    }

    #[On('erp-delete-catalogo')]
    public function deleteCatalogo(
        int|string $categoriaId = 0,
        int|string $registroId = 0
    ): void {
        $resolvedRegistroId = (int) $registroId;
        $resolvedCategoriaId = (int) $categoriaId;

        if ($resolvedRegistroId <= 0) {
            return;
        }

        try {
            /** @var CatalogoService $catalogoService */
            $catalogoService = app(CatalogoService::class);
            $deleted = $catalogoService->deleteById($resolvedRegistroId, $resolvedCategoriaId > 0 ? $resolvedCategoriaId : null);

            if (!$deleted) {
                $this->dispatch('notify', type: 'warning', message: 'No se pudo eliminar el catalogo.');

                return;
            }

            $this->dispatch('notify', type: 'success', message: 'Catalogo eliminado correctamente.');
            $this->dispatch('erp-catalogo-deleted', categoriaId: $resolvedCategoriaId, registroId: $resolvedRegistroId);
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('notify', type: 'error', message: 'Ocurrio un error al eliminar el catalogo.');
        }
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->showFieldCrudModal = false;
        $this->resetFormState();
        $this->categoriaId = 0;
        $this->categoriaSlot = 0;
        $this->categoriaNombre = '';
        $this->table = 'erp_catalogos';
    }

    public function save(): void
    {
        $codigoRules = ['nullable', 'string', 'max:50'];
        if (trim($this->codigo) !== '') {
            $codigoUnique = Rule::unique('erp_catalogos', 'codigo')
                ->where(function ($query) {
                    $query->where('categoria_id', $this->categoriaId)
                        ->whereNull('deleted_at');
                });

            if ($this->registroId !== null) {
                $codigoUnique = $codigoUnique->ignore($this->registroId);
            }

            $codigoRules[] = $codigoUnique;
        }

        $this->validate([
            'categoriaId' => ['required', 'integer', 'min:1', 'exists:erp_categorias,id'],
            'subcategoria' => ['nullable', 'integer', 'exists:erp_subcategorias,id'],
            'codigo' => $codigoRules,
            'descripcion' => ['nullable', 'string', 'max:500'],
            'estado' => ['required', 'integer', 'in:0,1'],
        ], [
            'categoriaId.required' => 'La categoria es obligatoria.',
            'categoriaId.exists' => 'La categoria seleccionada no existe.',
            'subcategoria.exists' => 'La subcategoria seleccionada no existe.',
            'codigo.unique' => 'El codigo ya existe en esta categoria.',
        ]);

        try {
            $imageValue = $this->persistImageUpload();

            /** @var CatalogoService $catalogoService */
            $catalogoService = app(CatalogoService::class);

            $catalogo = $catalogoService->saveFromModal(
                registroId: $this->registroId,
                categoriaId: $this->categoriaId,
                subcategoria: $this->subcategoria,
                codigo: $this->codigo,
                descripcion: $this->descripcion,
                estado: $this->estado,
                values: $this->values,
                imagen: $imageValue,
                userId: auth()->id()
            );

            $this->registroId = (int) $catalogo->id;
            $this->dispatch('notify', type: 'success', message: 'Catalogo guardado correctamente.');
            $this->dispatch('erp-catalogo-saved', categoriaId: $this->categoriaId, registroId: $this->registroId);
            $this->closeModal();
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('notify', type: 'error', message: 'No se pudo guardar el catalogo.');
        }
    }

    public function render()
    {
        return view('erp::livewire.catalogo-modal-manager');
    }

    private function loadCatalogoForEdit(int $registroId): void
    {
        /** @var CatalogoService $catalogoService */
        $catalogoService = app(CatalogoService::class);
        $catalogo = $catalogoService->findById($registroId);

        if (!$catalogo) {
            $this->dispatch('notify', type: 'warning', message: 'No se encontro el registro para editar.');

            return;
        }

        $catalogoCategoriaId = (int) ($catalogo->categoria_id ?? 0);
        if ($catalogoCategoriaId > 0 && $catalogoCategoriaId !== $this->categoriaId) {
            $this->categoriaId = $catalogoCategoriaId;
            $this->categoriaSlot = (int) ($this->categoriaSlotById[(string) $catalogoCategoriaId] ?? 0);
            $meta = $this->categoriaMeta();
            $this->categoriaNombre = (string) ($meta['nombre'] ?? $this->categoriaNombre);
            $this->initializeDynamicValues();
        }

        $this->registroId = (int) $catalogo->id;
        $this->subcategoria = (string) ($catalogo->subcategoria_id ?? '');
        $this->codigo = (string) ($catalogo->codigo ?? '');
        $this->descripcion = (string) ($catalogo->descripcion ?? '');
        $this->estado = (int) ($catalogo->estado ?? 1);

        foreach (CatalogoFieldMap::CHARACTERISTIC_FIELDS as $field => $column) {
            if (!array_key_exists($field, $this->values)) {
                continue;
            }

            $this->values[$field] = (string) ($catalogo->{$column} ?? '');
        }

        if (array_key_exists('imagen', $this->values)) {
            $this->values['imagen'] = (string) ($catalogo->imagen ?? '');
        }
    }

    private function prepareDuplicateFromLoadedRecord(): void
    {
        $this->registroId = null;
        $this->codigo = '';
        $this->imagenUpload = null;
    }

    private function resetFormState(): void
    {
        $this->registroId = null;
        $this->subcategoria = '';
        $this->codigo = '';
        $this->descripcion = '';
        $this->estado = 1;
        $this->imagenUpload = null;
        $this->values = [];
        $this->showFieldCrudModal = false;
        $this->restoreMainModalAfterCrud = false;
        $this->crudField = '';
        $this->crudTable = '';
        $this->crudCodigo = '';
        $this->crudNombre = '';
        $this->crudEstado = 1;
        $this->crudRecordId = null;
        $this->crudRows = [];
        $this->crudRelationLabel = '';
        $this->crudRelationColumn = null;
        $this->crudRelationValue = null;
        $this->crudRelationName = '';
        $this->crudRelationMessage = '';
        $this->crudHasCodigo = true;
        $this->crudHasEstado = true;
        $this->resetValidation();
    }

    private function initializeDynamicValues(): void
    {
        $current = $this->values;
        $this->values = [];

        foreach ($this->dynamicFields() as $field) {
            $this->values[$field] = (string) ($current[$field] ?? '');
        }
    }

    private function rebuildDescripcion(): void
    {
        $parts = [];

        foreach ($this->autocompleteFields() as $field) {
            $value = $this->resolveAutocompleteValue($field);
            if ($value !== '') {
                $parts[] = $value;
            }
        }

        if ($parts === []) {
            $meta = $this->categoriaMeta();
            $categoriaCode = trim((string) ($meta['codigo'] ?? ''));
            if ($categoriaCode !== '') {
                $parts[] = $categoriaCode;
            }
        }

        $this->descripcion = implode(' ', $parts);
    }

    private function resolveAutocompleteValue(string $field): string
    {
        if ($field === 'categoria') {
            $meta = $this->categoriaMeta();

            return trim((string) ($meta['codigo'] ?? ''));
        }

        if ($field === 'codigo') {
            return trim($this->codigo);
        }

        if ($field === 'subcategoria') {
            return $this->resolveOptionCode('subcategoria', $this->subcategoria);
        }

        return $this->resolveOptionCode($field, $this->values[$field] ?? '');
    }

    private function resolveOptionCode(string $field, mixed $selectedValue): string
    {
        if ($selectedValue === null || $selectedValue === '') {
            return '';
        }

        $selected = (string) $selectedValue;
        foreach ($this->fieldOptions($field) as $option) {
            if ((string) ($option['id'] ?? '') !== $selected) {
                continue;
            }

            return trim((string) ($option['codigo'] ?? ''));
        }

        return '';
    }

    public function fieldLabel(string $field): string
    {
        return self::FIELD_LABELS[$field] ?? ucfirst(str_replace('_', ' ', $field));
    }

    public function addCatalogOption(string $field): void
    {
        $this->openFieldCrudModal($field);
    }

    #[Computed]
    public function isCrudRelationReady(): bool
    {
        if ($this->crudRelationColumn === null) {
            return true;
        }

        return $this->crudRelationValue !== null;
    }

    public function openFieldCrudModal(string $field): void
    {
        $field = trim($field);
        if (!$this->canManageField($field)) {
            return;
        }

        $table = $this->resolveFieldTable($field);
        if ($table === null) {
            $this->dispatch('notify', type: 'warning', message: 'No existe configuracion de tabla para el campo seleccionado.');

            return;
        }

        if (!Schema::hasTable($table)) {
            $this->dispatch('notify', type: 'warning', message: "La tabla {$table} no existe.");

            return;
        }

        $this->crudField = $field;
        $this->crudTable = $table;
        $this->crudRows = [];
        $this->crudRecordId = null;
        $this->crudCodigo = '';
        $this->crudNombre = '';
        $this->crudEstado = 1;
        $this->crudHasCodigo = Schema::hasColumn($table, 'codigo');
        $this->crudHasEstado = Schema::hasColumn($table, 'estado');
        $this->resetValidation(['crudCodigo', 'crudNombre', 'crudEstado']);

        $relation = $this->resolveCrudRelationContext($field, $table);
        $this->crudRelationLabel = $relation['label'] ?? '';
        $this->crudRelationColumn = $relation['column'] ?? null;
        $this->crudRelationValue = $relation['value'] ?? null;
        $this->crudRelationName = $relation['name'] ?? '';
        $this->crudRelationMessage = $relation['message'] ?? '';

        $this->loadCrudRows();

        $selectedValue = $this->currentFieldSelectedValue($field);
        if ($selectedValue !== '') {
            $this->editFieldCrudRecord((int) $selectedValue);
        }

        $this->restoreMainModalAfterCrud = $this->showModal;
        $this->showModal = false;
        $this->showFieldCrudModal = true;
    }

    public function closeFieldCrudModal(): void
    {
        $this->showFieldCrudModal = false;
        $this->showModal = $this->restoreMainModalAfterCrud;
        $this->restoreMainModalAfterCrud = false;
        $this->crudField = '';
        $this->crudTable = '';
        $this->crudRows = [];
        $this->crudRecordId = null;
        $this->crudCodigo = '';
        $this->crudNombre = '';
        $this->crudEstado = 1;
        $this->crudRelationLabel = '';
        $this->crudRelationColumn = null;
        $this->crudRelationValue = null;
        $this->crudRelationName = '';
        $this->crudRelationMessage = '';
        $this->crudHasCodigo = true;
        $this->crudHasEstado = true;
        $this->resetValidation(['crudCodigo', 'crudNombre', 'crudEstado']);
    }

    public function startFieldCrudCreate(): void
    {
        if ($this->crudTable === '') {
            return;
        }

        $this->crudRecordId = null;
        $this->crudCodigo = '';
        $this->crudNombre = '';
        $this->crudEstado = 1;
        $this->resetValidation(['crudCodigo', 'crudNombre', 'crudEstado']);
    }

    public function editFieldCrudRecord(int|string $id): void
    {
        $recordId = (int) $id;
        if ($recordId <= 0 || $this->crudTable === '') {
            return;
        }

        $query = DB::table($this->crudTable)->where('id', $recordId);

        if (Schema::hasColumn($this->crudTable, 'deleted_at')) {
            $query->whereNull('deleted_at');
        }

        if ($this->crudRelationColumn !== null && $this->crudRelationValue !== null) {
            $query->where($this->crudRelationColumn, $this->crudRelationValue);
        }

        $row = $query->first();
        if (!$row) {
            $this->dispatch('notify', type: 'warning', message: 'El registro no fue encontrado en el contexto actual.');

            return;
        }

        $this->crudRecordId = (int) $row->id;
        $this->crudCodigo = trim((string) ($row->codigo ?? ''));
        $this->crudNombre = trim((string) ($row->nombre ?? ''));
        $this->crudEstado = (int) ($row->estado ?? 1);
        $this->resetValidation(['crudCodigo', 'crudNombre', 'crudEstado']);
    }

    public function saveFieldCrudRecord(): void
    {
        if ($this->crudTable === '') {
            return;
        }

        if (!$this->isCrudRelationReady()) {
            $message = $this->crudRelationMessage !== ''
                ? $this->crudRelationMessage
                : 'Seleccione el campo relacionado antes de guardar.';

            $this->dispatch('notify', type: 'warning', message: $message);

            return;
        }

        $nameMax = Schema::hasColumn($this->crudTable, 'nombre') ? 100 : 255;
        $rules = [
            'crudNombre' => ['required', 'string', "max:{$nameMax}"],
        ];

        if (Schema::hasColumn($this->crudTable, 'codigo')) {
            $codigoUnique = Rule::unique($this->crudTable, 'codigo');
            if ($this->crudRecordId) {
                $codigoUnique = $codigoUnique->ignore($this->crudRecordId);
            }

            if (Schema::hasColumn($this->crudTable, 'deleted_at')) {
                $codigoUnique = $codigoUnique->where(function ($query) {
                    $query->whereNull('deleted_at');
                });
            }

            $rules['crudCodigo'] = ['required', 'string', 'max:50', $codigoUnique];
        }

        if (Schema::hasColumn($this->crudTable, 'estado')) {
            $rules['crudEstado'] = ['required', 'integer', 'in:0,1'];
        }

        $messages = [
            'crudCodigo.required' => 'El codigo es obligatorio.',
            'crudCodigo.unique' => 'El codigo ya existe.',
            'crudNombre.required' => 'El nombre es obligatorio.',
        ];

        $this->validate($rules, $messages);

        $payload = [
            'nombre' => trim($this->crudNombre),
        ];

        if (Schema::hasColumn($this->crudTable, 'codigo')) {
            $payload['codigo'] = trim($this->crudCodigo);
        }

        if (Schema::hasColumn($this->crudTable, 'estado')) {
            $payload['estado'] = (int) $this->crudEstado;
        }

        if ($this->crudRelationColumn !== null && $this->crudRelationValue !== null) {
            $payload[$this->crudRelationColumn] = $this->crudRelationValue;
        }

        $now = now();
        if (Schema::hasColumn($this->crudTable, 'updated_at')) {
            $payload['updated_at'] = $now;
        }

        if ($this->crudRecordId) {
            DB::table($this->crudTable)
                ->where('id', $this->crudRecordId)
                ->update($payload);

            $savedId = $this->crudRecordId;
            $actionText = 'actualizado';
        } else {
            if (Schema::hasColumn($this->crudTable, 'created_at')) {
                $payload['created_at'] = $now;
            }

            if (Schema::hasColumn($this->crudTable, 'deleted_at')) {
                $payload['deleted_at'] = null;
            }

            $savedId = (int) DB::table($this->crudTable)->insertGetId($payload);
            $this->crudRecordId = $savedId;
            $actionText = 'creado';
        }

        $this->loadCrudRows();

        if ($savedId > 0) {
            $this->currentFieldSelectedValue($this->crudField, (string) $savedId);
        }

        $this->refreshFieldOptionsByTable($this->crudTable);
        $this->syncDependentFields($this->crudField);
        $this->rebuildDescripcion();

        $this->dispatch('notify', type: 'success', message: 'Registro ' . $actionText . ' correctamente.');
    }

    public function deleteFieldCrudRecord(int|string|null $id = null): void
    {
        if ($this->crudTable === '') {
            return;
        }

        $recordId = $id !== null ? (int) $id : (int) ($this->crudRecordId ?? 0);
        if ($recordId <= 0) {
            return;
        }

        $query = DB::table($this->crudTable)->where('id', $recordId);
        if (Schema::hasColumn($this->crudTable, 'deleted_at')) {
            $query->whereNull('deleted_at');
        }

        if ($this->crudRelationColumn !== null && $this->crudRelationValue !== null) {
            $query->where($this->crudRelationColumn, $this->crudRelationValue);
        }

        $target = $query->first();
        if (!$target) {
            $this->dispatch('notify', type: 'warning', message: 'No se pudo eliminar el registro.');

            return;
        }

        if (Schema::hasColumn($this->crudTable, 'deleted_at')) {
            $update = ['deleted_at' => now()];
            if (Schema::hasColumn($this->crudTable, 'updated_at')) {
                $update['updated_at'] = now();
            }
            DB::table($this->crudTable)->where('id', $recordId)->update($update);
        } else {
            DB::table($this->crudTable)->where('id', $recordId)->delete();
        }

        if ($this->currentFieldSelectedValue($this->crudField) === (string) $recordId) {
            $this->currentFieldSelectedValue($this->crudField, '');
        }

        $this->startFieldCrudCreate();
        $this->loadCrudRows();
        $this->refreshFieldOptionsByTable($this->crudTable);
        $this->syncDependentFields($this->crudField);
        $this->rebuildDescripcion();

        $this->dispatch('notify', type: 'success', message: 'Registro eliminado correctamente.');
    }

    private function canManageField(string $field): bool
    {
        if ($field === '' || $field === 'imagen') {
            return false;
        }

        $allowedFields = array_unique(array_merge(['subcategoria'], $this->dynamicFields()));

        return in_array($field, $allowedFields, true);
    }

    private function resolveFieldTable(string $field): ?string
    {
        return self::FIELD_TABLES[$field] ?? null;
    }

    /**
     * @return array{label:string,column:?string,value:?int,name:string,message:string}
     */
    private function resolveCrudRelationContext(string $field, string $table): array
    {
        if ($field === 'detallecolor') {
            $column = $this->resolveExistingColumn($table, ['color_id', 'erp_color_id']);
            $colorId = (int) ($this->values['color'] ?? 0);
            $colorValue = $colorId > 0 ? $colorId : null;
            $colorName = $this->resolveOptionName('color', $colorValue);

            return [
                'label' => 'Color',
                'column' => $column,
                'value' => $colorValue,
                'name' => $colorName,
                'message' => 'Seleccione Color antes de crear/editar Detalle de color.',
            ];
        }

        if (in_array($field, self::CATEGORY_SCOPED_FIELDS, true)) {
            $column = $this->resolveExistingColumn($table, ['categoria_id', 'erp_categoria_id']);
            $meta = $this->categoriaMeta();
            $categoriaName = trim((string) ($meta['nombre'] ?? ''));
            if ($categoriaName === '') {
                $categoriaName = trim($this->categoriaNombre);
            }

            return [
                'label' => 'Categoria',
                'column' => $column,
                'value' => $this->categoriaId > 0 ? $this->categoriaId : null,
                'name' => $categoriaName,
                'message' => 'Categoria no disponible para este registro.',
            ];
        }

        return [
            'label' => '',
            'column' => null,
            'value' => null,
            'name' => '',
            'message' => '',
        ];
    }

    private function resolveExistingColumn(string $table, array $candidates): ?string
    {
        foreach ($candidates as $candidate) {
            if (Schema::hasColumn($table, $candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function resolveOptionName(string $field, ?int $selectedId): string
    {
        if ($selectedId === null || $selectedId <= 0) {
            return '';
        }

        foreach ($this->fieldOptions($field) as $option) {
            if ((int) ($option['id'] ?? 0) !== $selectedId) {
                continue;
            }

            return trim((string) ($option['nombre'] ?? ''));
        }

        return '';
    }

    private function loadCrudRows(): void
    {
        if ($this->crudTable === '' || !Schema::hasTable($this->crudTable)) {
            $this->crudRows = [];

            return;
        }

        $query = DB::table($this->crudTable)->select('id', 'nombre');

        if (Schema::hasColumn($this->crudTable, 'codigo')) {
            $query->addSelect('codigo');
        }

        if (Schema::hasColumn($this->crudTable, 'estado')) {
            $query->addSelect('estado');
        }

        if (Schema::hasColumn($this->crudTable, 'deleted_at')) {
            $query->whereNull('deleted_at');
        }

        if ($this->crudRelationColumn !== null) {
            if ($this->crudRelationValue === null) {
                $this->crudRows = [];

                return;
            }

            $query->where($this->crudRelationColumn, $this->crudRelationValue);
        }

        $this->crudRows = $query
            ->orderBy('nombre')
            ->get()
            ->map(fn($row) => [
                'id' => (int) $row->id,
                'codigo' => (string) ($row->codigo ?? ''),
                'nombre' => (string) ($row->nombre ?? ''),
                'estado' => isset($row->estado) ? (int) $row->estado : 1,
            ])
            ->values()
            ->all();
    }

    private function currentFieldSelectedValue(string $field, ?string $newValue = null): string
    {
        if ($field === 'subcategoria') {
            if ($newValue !== null) {
                $this->subcategoria = $newValue;
            }

            return (string) $this->subcategoria;
        }

        if ($newValue !== null) {
            $this->values[$field] = $newValue;
        }

        return (string) ($this->values[$field] ?? '');
    }

    private function refreshFieldOptionsByTable(string $table): void
    {
        if ($this->categoriaId <= 0) {
            return;
        }

        $options = $this->fetchOptionsFromTable($table, $this->categoriaId);
        $categoriaKey = (string) $this->categoriaId;

        if (!isset($this->catalogoOptions[$categoriaKey]) || !is_array($this->catalogoOptions[$categoriaKey])) {
            $this->catalogoOptions[$categoriaKey] = [];
        }

        foreach (self::FIELD_TABLES as $field => $mappedTable) {
            if ($mappedTable !== $table) {
                continue;
            }

            $this->catalogoOptions[$categoriaKey][$field] = $options;
        }
    }

    /**
     * @return array<int,array{id:mixed,nombre:string,codigo:?string,color_id:?string}>
     */
    private function fetchOptionsFromTable(string $table, ?int $categoriaId = null): array
    {
        if (!Schema::hasTable($table)) {
            return [];
        }

        $query = DB::table($table)->select('id', 'nombre');

        if (Schema::hasColumn($table, 'codigo')) {
            $query->addSelect('codigo');
        }

        if (Schema::hasColumn($table, 'color_id')) {
            $query->addSelect('color_id');
        }

        if (Schema::hasColumn($table, 'deleted_at')) {
            $query->whereNull('deleted_at');
        }

        $categoriaColumn = $this->resolveExistingColumn($table, ['categoria_id', 'erp_categoria_id']);
        if ($categoriaColumn !== null) {
            if ($categoriaId === null) {
                return [];
            }

            $query->where($categoriaColumn, $categoriaId);
        }

        return $query->orderBy('nombre')->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'nombre' => $item->nombre,
                'codigo' => $item->codigo ?? null,
                'color_id' => isset($item->color_id) ? (string) $item->color_id : null,
            ])
            ->values()
            ->all();
    }

    private function persistImageUpload(): ?string
    {
        if (!$this->imagenUpload || !method_exists($this->imagenUpload, 'getRealPath')) {
            $selected = trim((string) ($this->values['imagen'] ?? ''));

            return $selected !== '' ? $selected : null;
        }

        $extension = strtolower((string) ($this->imagenUpload->getClientOriginalExtension() ?? ''));
        $extension = preg_replace('/[^a-z0-9]/', '', $extension) ?: 'png';

        $fileName = 'catalogo_' . now()->format('Ymd_His') . '_' . Str::lower(Str::random(8)) . '.' . $extension;
        $directory = public_path('src/img/articulos');

        File::ensureDirectoryExists($directory);

        $source = $this->imagenUpload->getRealPath();
        if (!is_string($source) || $source === '' || !is_file($source)) {
            throw new \RuntimeException('Archivo temporal de imagen no valido.');
        }

        $target = $directory . DIRECTORY_SEPARATOR . $fileName;
        if (!File::copy($source, $target)) {
            throw new \RuntimeException('No se pudo copiar la imagen al destino final.');
        }

        $this->values['imagen'] = $fileName;

        return $fileName;
    }

    private function fallbackBarcodeDataUri(string $message): string
    {
        $safeMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
        $safeText = htmlspecialchars($this->barcodePayload(), ENT_QUOTES, 'UTF-8');
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="640" height="120" viewBox="0 0 640 120">'
            . '<rect width="100%" height="100%" fill="#f8f9fa"/>'
            . '<rect x="1" y="1" width="638" height="118" fill="none" stroke="#d9dee3"/>'
            . '<text x="12" y="38" font-family="monospace" font-size="14" fill="#d32f2f">ERROR BARCODE</text>'
            . '<text x="12" y="62" font-family="monospace" font-size="12" fill="#566a7f">' . $safeMessage . '</text>'
            . '<text x="12" y="92" font-family="monospace" font-size="12" fill="#566a7f">TEXT: ' . $safeText . '</text>'
            . '</svg>';

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}

