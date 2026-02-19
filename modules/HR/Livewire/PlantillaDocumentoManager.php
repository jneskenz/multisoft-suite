<?php

namespace Modules\HR\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\HR\Models\CategoriaDocumento;
use Modules\HR\Models\PlantillaDocumento;
use Modules\HR\Models\PlantillaSeccion;
use Modules\HR\Models\PlantillaSeccionAsignada;
use Modules\HR\Models\PlantillaVersion;
use Modules\HR\Models\TipoDocumento;

class PlantillaDocumentoManager extends Component
{
   use WithPagination;

   #[Url(as: 'q')]
   public string $search = '';

   #[Url]
   public string $tipoFilter = '';

   #[Url]
   public string $statusFilter = '';

   public string $sortField = 'created_at';
   public string $sortDirection = 'desc';
   public int $perPage = 10;

   // Modal states
   public bool $showModal = false;
   public bool $showDeleteModal = false;
   public bool $showRestoreModal = false;
   public bool $showForceDeleteModal = false;
   public bool $showHistorialModal = false;
   public bool $isEditing = false;
   public bool $showTrashed = false;
   public ?int $itemId = null;
   public ?int $historialPlantillaId = null;

   // Form fields
   public string $codigo = '';
   public string $nombre = '';
   public string $descripcion = '';
   public ?int $tipo_documento_id = null;
   public ?int $categoria_documento_id = null;
   public string $idioma = 'es';
   public string $formato_papel = 'A4';
   public string $orientacion = 'vertical';
   public string $version = '1.0';
   public string $contenido_html = '';
   public bool $es_predeterminada = false;
   public string $estado = '1';

   // Secciones asignadas
   public array $seccionesAsignadas = [];
   public string $seccionIdToAdd = '';

   protected array $allowedSortFields = [
      'id',
      'codigo',
      'nombre',
      'version',
      'estado',
      'created_at',
   ];

   #[Computed]
   public function plantillas()
   {
      $query = PlantillaDocumento::query()
         ->with(['tipoDocumento:id,nombre,codigo', 'categoriaDocumento:id,nombre'])
         ->when($this->search, function ($q) {
            $q->where(function ($q) {
               $q->where('nombre', 'like', "%{$this->search}%")
                  ->orWhere('codigo', 'like', "%{$this->search}%")
                  ->orWhere('descripcion', 'like', "%{$this->search}%");
            });
         })
         ->when($this->tipoFilter !== '', function ($q) {
            $q->where('tipo_documento_id', (int) $this->tipoFilter);
         })
         ->when($this->statusFilter !== '', function ($q) {
            $q->where('estado', $this->statusFilter);
         })
         ->orderBy($this->sortField, $this->sortDirection);

      if ($this->showTrashed) {
         $query->onlyTrashed();
      }

      return $query->paginate($this->perPage);
   }

   #[Computed]
   public function tiposDocumento()
   {
      return TipoDocumento::query()->active()->orderBy('nombre')->get(['id', 'nombre', 'codigo']);
   }

   #[Computed]
   public function categoriasDocumento()
   {
      if ($this->tipo_documento_id) {
         return CategoriaDocumento::query()
            ->where('tipo_documento_id', $this->tipo_documento_id)
            ->active()
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'codigo']);
      }
      return collect();
   }

   #[Computed]
   public function seccionesDisponibles()
   {
      $assignedIds = collect($this->seccionesAsignadas)->pluck('seccion_id')->toArray();

      // Get the current tipo_documento code to filter by aplicable_a
      $tipoDocCodigo = null;
      if ($this->tipo_documento_id) {
         $tipoDocCodigo = TipoDocumento::where('id', $this->tipo_documento_id)->value('codigo');
      }

      return PlantillaSeccion::query()
         ->where('estado', '1')
         ->whereNotIn('id', $assignedIds)
         ->when($tipoDocCodigo, function ($q) use ($tipoDocCodigo) {
            $q->where(function ($q) use ($tipoDocCodigo) {
               $q->whereNull('aplicable_a')
                  ->orWhereJsonContains('aplicable_a', $tipoDocCodigo);
            });
         })
         ->orderBy('nombre')
         ->get(['id', 'nombre', 'codigo', 'categoria']);
   }

   #[Computed]
   public function versionesHistorial()
   {
      if (!$this->historialPlantillaId) {
         return collect();
      }
      return PlantillaVersion::where('plantilla_id', $this->historialPlantillaId)
         ->orderByDesc('fecha_version')
         ->get();
   }

   protected function rules(): array
   {
      return [
         'codigo' => [
            'required',
            'string',
            'max:20',
            Rule::unique('hr_plantillas_documento', 'codigo')->ignore($this->itemId),
         ],
         'nombre' => ['required', 'string', 'max:200'],
         'descripcion' => ['nullable', 'string'],
         'tipo_documento_id' => ['required', 'exists:hr_tipos_documento,id'],
         'categoria_documento_id' => ['nullable', 'exists:hr_categorias_documento,id'],
         'idioma' => ['required', 'string', 'in:es,en'],
         'formato_papel' => ['required', 'string', 'in:A4,Letter'],
         'orientacion' => ['required', 'string', 'in:vertical,horizontal'],
         'version' => ['required', 'string', 'max:20'],
         'contenido_html' => ['nullable', 'string'],
         'es_predeterminada' => ['boolean'],
         'estado' => ['required', 'in:1,0'],
      ];
   }

   protected function messages(): array
   {
      return [
         'codigo.required' => 'El código es obligatorio.',
         'codigo.unique' => 'Este código ya existe.',
         'nombre.required' => 'El nombre es obligatorio.',
         'tipo_documento_id.required' => 'El tipo de documento es obligatorio.',
      ];
   }

   public function sortBy(string $field): void
   {
      if (!in_array($field, $this->allowedSortFields, true)) {
         return;
      }

      if ($this->sortField === $field) {
         $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
      } else {
         $this->sortField = $field;
         $this->sortDirection = 'asc';
      }
   }

   public function updatedSearch(): void
   {
      $this->resetPage();
   }

   public function updatedTipoDocumentoId(): void
   {
      unset($this->seccionesDisponibles);
      unset($this->categoriasDocumento);
      $this->categoria_documento_id = null;
   }

   public function clearFilters(): void
   {
      $this->reset(['search', 'tipoFilter', 'statusFilter']);
      $this->resetPage();
   }

   #[On('openCreatePlantillaModal')]
   public function create(): void
   {
      $this->resetForm();
      $this->isEditing = false;
      $this->showModal = true;
   }

   public function edit(int $id): void
   {
      $item = PlantillaDocumento::findOrFail($id);

      $this->itemId = $item->id;
      $this->codigo = $item->codigo;
      $this->nombre = $item->nombre;
      $this->descripcion = $item->descripcion ?? '';
      $this->tipo_documento_id = $item->tipo_documento_id;
      $this->categoria_documento_id = $item->categoria_documento_id;
      $this->idioma = $item->idioma ?? 'es';
      $this->formato_papel = $item->formato_papel ?? 'A4';
      $this->orientacion = $item->orientacion ?? 'vertical';
      $this->version = $item->version ?? '1.0';
      $this->contenido_html = $item->contenido_html ?? '';
      $this->es_predeterminada = (bool) $item->es_predeterminada;
      $this->estado = $item->estado ?? '1';

      // Load assigned sections
      $this->seccionesAsignadas = PlantillaSeccionAsignada::where('plantilla_id', $id)
         ->join('hr_plantillas_secciones', 'hr_plantillas_secciones.id', '=', 'seccion_id')
         ->select(
            'hr_plantillas_secciones_asignadas.id as pivot_id',
            'seccion_id',
            'hr_plantillas_secciones.nombre',
            'hr_plantillas_secciones.codigo',
            'hr_plantillas_secciones.categoria',
            'hr_plantillas_secciones_asignadas.orden',
            'hr_plantillas_secciones_asignadas.ubicacion',
            'hr_plantillas_secciones_asignadas.es_obligatoria',
            'hr_plantillas_secciones_asignadas.es_editable'
         )
         ->orderBy('hr_plantillas_secciones_asignadas.orden')
         ->get()
         ->toArray();

      $this->isEditing = true;
      $this->showModal = true;

      // Dispatch event to reload Quill editor content (wire:ignore blocks don't re-render)
      $this->dispatch('quill-load-content', ['html' => $this->contenido_html]);
   }

   public function save(): void
   {
      $this->validate();

      $data = [
         'codigo' => $this->codigo,
         'nombre' => $this->nombre,
         'descripcion' => $this->descripcion ?: null,
         'tipo_documento_id' => $this->tipo_documento_id,
         'categoria_documento_id' => $this->categoria_documento_id,
         'idioma' => $this->idioma,
         'formato_papel' => $this->formato_papel,
         'orientacion' => $this->orientacion,
         'version' => $this->version,
         'contenido_html' => $this->contenido_html ?: null,
         'es_predeterminada' => $this->es_predeterminada,
         'estado' => $this->estado,
      ];

      if ($this->isEditing) {
         $item = PlantillaDocumento::findOrFail($this->itemId);

         // Auto-version: save current state before updating
         if ($item->contenido_html !== $this->contenido_html || $item->version !== $this->version) {
            PlantillaVersion::create([
               'plantilla_id' => $item->id,
               'version' => $item->version ?? '1.0',
               'contenido_html' => $item->contenido_html ?? '',
               'contenido_texto' => strip_tags($item->contenido_html ?? ''),
               'configuracion' => json_encode([
                  'idioma' => $item->idioma,
                  'formato_papel' => $item->formato_papel,
                  'orientacion' => $item->orientacion,
                  'es_predeterminada' => $item->es_predeterminada,
               ]),
               'motivo_cambio' => 'Actualización desde panel de administración',
               'created_by' => Auth::id(),
            ]);
         }

         $item->update($data);

         // Sync assigned sections
         $this->syncSeccionesAsignadas($item->id);

         $message = __('Plantilla actualizada correctamente.');
      } else {
         if (empty($data['contenido_html'])) {
            $data['contenido_html'] = '<p>Contenido de la plantilla aquí...</p>';
         }
         $item = PlantillaDocumento::create($data);

         // Sync assigned sections for new template
         $this->syncSeccionesAsignadas($item->id);

         $message = __('Plantilla creada correctamente.');
      }

      $this->closeModal();
      $this->dispatch('notify', type: 'success', message: $message);
   }

   // --- Secciones asignadas ---

   public function addSeccion(): void
   {
      if (!$this->seccionIdToAdd) return;

      $seccion = PlantillaSeccion::find((int) $this->seccionIdToAdd);
      if (!$seccion) return;

      $maxOrden = collect($this->seccionesAsignadas)->max('orden') ?? 0;

      $this->seccionesAsignadas[] = [
         'pivot_id' => null,
         'seccion_id' => $seccion->id,
         'nombre' => $seccion->nombre,
         'codigo' => $seccion->codigo,
         'categoria' => $seccion->categoria,
         'orden' => $maxOrden + 1,
         'ubicacion' => 'cuerpo',
         'es_obligatoria' => false,
         'es_editable' => true,
      ];

      $this->seccionIdToAdd = '';
      unset($this->seccionesDisponibles);
   }

   public function removeSeccion(int $index): void
   {
      unset($this->seccionesAsignadas[$index]);
      $this->seccionesAsignadas = array_values($this->seccionesAsignadas);
      unset($this->seccionesDisponibles);
   }

   public function moveSeccion(int $index, string $direction): void
   {
      $swapWith = $direction === 'up' ? $index - 1 : $index + 1;
      if ($swapWith < 0 || $swapWith >= count($this->seccionesAsignadas)) return;

      $temp = $this->seccionesAsignadas[$index]['orden'];
      $this->seccionesAsignadas[$index]['orden'] = $this->seccionesAsignadas[$swapWith]['orden'];
      $this->seccionesAsignadas[$swapWith]['orden'] = $temp;

      $tempItem = $this->seccionesAsignadas[$index];
      $this->seccionesAsignadas[$index] = $this->seccionesAsignadas[$swapWith];
      $this->seccionesAsignadas[$swapWith] = $tempItem;
   }

   private function syncSeccionesAsignadas(int $plantillaId): void
   {
      // Remove all existing assignments
      PlantillaSeccionAsignada::where('plantilla_id', $plantillaId)->delete();

      // Insert new assignments
      foreach ($this->seccionesAsignadas as $seccion) {
         PlantillaSeccionAsignada::create([
            'plantilla_id' => $plantillaId,
            'seccion_id' => $seccion['seccion_id'],
            'orden' => $seccion['orden'],
            'ubicacion' => $seccion['ubicacion'] ?? 'cuerpo',
            'es_obligatoria' => $seccion['es_obligatoria'] ?? false,
            'es_editable' => $seccion['es_editable'] ?? true,
         ]);
      }
   }

   // --- Historial de versiones ---

   public function showHistorial(int $id): void
   {
      $this->historialPlantillaId = $id;
      unset($this->versionesHistorial);
      $this->showHistorialModal = true;
   }

   public function confirmDelete(int $id): void
   {
      $this->itemId = $id;
      $this->showDeleteModal = true;
   }

   public function delete(): void
   {
      PlantillaDocumento::findOrFail($this->itemId)->delete();
      $this->showDeleteModal = false;
      $this->itemId = null;
      $this->dispatch('notify', type: 'success', message: 'Plantilla eliminada correctamente.');
   }

   public function toggleEstado(int $id): void
   {
      $item = PlantillaDocumento::findOrFail($id);
      $newEstado = $item->estado === '1' ? '0' : '1';
      $item->update(['estado' => $newEstado]);
      $this->dispatch('notify', type: 'success', message: "Plantilla actualizada correctamente.");
   }

   public function toggleTrashed(): void
   {
      $this->showTrashed = !$this->showTrashed;
      $this->resetPage();
   }

   public function confirmRestore(int $id): void
   {
      $this->itemId = $id;
      $this->showRestoreModal = true;
   }

   public function restore(): void
   {
      $item = PlantillaDocumento::onlyTrashed()->find($this->itemId);
      if ($item) {
         $item->restore();
         $this->dispatch('notify', type: 'success', message: 'Plantilla restaurada correctamente.');
      }
      $this->showRestoreModal = false;
      $this->itemId = null;
   }

   public function confirmForceDelete(int $id): void
   {
      $this->itemId = $id;
      $this->showForceDeleteModal = true;
   }

   public function forceDelete(): void
   {
      $item = PlantillaDocumento::onlyTrashed()->find($this->itemId);
      if ($item) {
         $item->forceDelete();
         $this->dispatch('notify', type: 'success', message: 'Plantilla eliminada permanentemente.');
      }
      $this->showForceDeleteModal = false;
      $this->itemId = null;
   }

   public function closeModal(): void
   {
      $this->showModal = false;
      $this->showDeleteModal = false;
      $this->showRestoreModal = false;
      $this->showForceDeleteModal = false;
      $this->showHistorialModal = false;
      $this->historialPlantillaId = null;
      $this->resetForm();
   }

   private function resetForm(): void
   {
      $this->reset([
         'itemId',
         'codigo',
         'nombre',
         'descripcion',
         'tipo_documento_id',
         'categoria_documento_id',
         'idioma',
         'formato_papel',
         'orientacion',
         'version',
         'contenido_html',
         'es_predeterminada',
         'estado',
         'seccionesAsignadas',
         'seccionIdToAdd',
      ]);
      $this->idioma = 'es';
      $this->formato_papel = 'A4';
      $this->orientacion = 'vertical';
      $this->version = '1.0';
      $this->estado = '1';
      $this->seccionesAsignadas = [];
      $this->resetValidation();
   }

   public function render()
   {
      return view('hr::livewire.plantilla-documento-manager');
   }
}
