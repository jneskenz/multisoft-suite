<?php

namespace Modules\HR\Livewire;

use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\HR\Models\PlantillaSeccion;
use Modules\HR\Models\TipoDocumento;

class SeccionManager extends Component
{
   use WithPagination;

   #[Url(as: 'q')]
   public string $search = '';

   #[Url]
   public string $categoriaFilter = '';

   #[Url]
   public string $statusFilter = '';

   public string $sortField = 'orden';
   public string $sortDirection = 'asc';
   public int $perPage = 10;

   // Modal states
   public bool $showModal = false;
   public bool $showDeleteModal = false;
   public bool $showRestoreModal = false;
   public bool $showForceDeleteModal = false;
   public bool $isEditing = false;
   public bool $showTrashed = false;
   public ?int $itemId = null;

   // Form fields
   public string $codigo = '';
   public string $nombre = '';
   public string $descripcion = '';
   public string $contenido_html = '';
   public string $categoria = '';
   public array $aplicable_a = [];
   public bool $es_obligatoria = false;
   public string $estado = '1';
   public int $orden = 0;

   protected array $allowedSortFields = [
      'id',
      'codigo',
      'nombre',
      'categoria',
      'estado',
      'orden',
      'created_at',
   ];

   #[Computed]
   public function secciones()
   {
      $query = PlantillaSeccion::query()
         ->when($this->search, function ($q) {
            $q->where(function ($q) {
               $q->where('nombre', 'like', "%{$this->search}%")
                  ->orWhere('codigo', 'like', "%{$this->search}%")
                  ->orWhere('descripcion', 'like', "%{$this->search}%");
            });
         })
         ->when($this->categoriaFilter !== '', function ($q) {
            $q->where('categoria', $this->categoriaFilter);
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

   protected function rules(): array
   {
      return [
         'codigo' => [
            'required',
            'string',
            'max:20',
            Rule::unique('hr_plantillas_secciones', 'codigo')->ignore($this->itemId),
         ],
         'nombre' => ['required', 'string', 'max:200'],
         'descripcion' => ['nullable', 'string'],
         'contenido_html' => ['required', 'string'],
         'categoria' => ['nullable', 'string', 'in:encabezado,cuerpo,clausula,footer,firma'],
         'aplicable_a' => ['nullable', 'array'],
         'aplicable_a.*' => ['string'],
         'es_obligatoria' => ['boolean'],
         'estado' => ['required', 'in:1,0'],
         'orden' => ['integer', 'min:0'],
      ];
   }

   protected function messages(): array
   {
      return [
         'codigo.required' => 'El código es obligatorio.',
         'codigo.unique' => 'Este código ya existe.',
         'nombre.required' => 'El nombre es obligatorio.',
         'contenido_html.required' => 'El contenido HTML es obligatorio.',
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

   public function clearFilters(): void
   {
      $this->reset(['search', 'categoriaFilter', 'statusFilter']);
      $this->resetPage();
   }

   #[On('openCreateSeccionModal')]
   public function create(): void
   {
      $this->resetForm();
      $this->isEditing = false;
      $this->showModal = true;
   }

   public function edit(int $id): void
   {
      $item = PlantillaSeccion::findOrFail($id);

      $this->itemId = $item->id;
      $this->codigo = $item->codigo;
      $this->nombre = $item->nombre;
      $this->descripcion = $item->descripcion ?? '';
      $this->contenido_html = $item->contenido_html ?? '';
      $this->categoria = $item->categoria ?? '';
      $this->aplicable_a = $item->aplicable_a ?? [];
      $this->es_obligatoria = (bool) $item->es_obligatoria;
      $this->estado = $item->estado ?? '1';
      $this->orden = (int) $item->orden;

      $this->isEditing = true;
      $this->showModal = true;
   }

   public function save(): void
   {
      $this->validate();

      $data = [
         'codigo' => $this->codigo,
         'nombre' => $this->nombre,
         'descripcion' => $this->descripcion ?: null,
         'contenido_html' => $this->contenido_html,
         'categoria' => $this->categoria ?: null,
         'aplicable_a' => !empty($this->aplicable_a) ? $this->aplicable_a : null,
         'es_obligatoria' => $this->es_obligatoria,
         'estado' => $this->estado,
         'orden' => $this->orden,
      ];

      if ($this->isEditing) {
         $item = PlantillaSeccion::findOrFail($this->itemId);
         $item->update($data);
         $message = __('Sección actualizada correctamente.');
      } else {
         PlantillaSeccion::create($data);
         $message = __('Sección creada correctamente.');
      }

      $this->closeModal();
      $this->dispatch('notify', type: 'success', message: $message);
   }

   public function confirmDelete(int $id): void
   {
      $this->itemId = $id;
      $this->showDeleteModal = true;
   }

   public function delete(): void
   {
      PlantillaSeccion::findOrFail($this->itemId)->delete();
      $this->showDeleteModal = false;
      $this->itemId = null;
      $this->dispatch('notify', type: 'success', message: 'Sección eliminada correctamente.');
   }

   public function toggleEstado(int $id): void
   {
      $item = PlantillaSeccion::findOrFail($id);
      $newEstado = $item->estado === '1' ? '0' : '1';
      $item->update(['estado' => $newEstado]);
      $this->dispatch('notify', type: 'success', message: "Sección actualizada correctamente.");
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
      $item = PlantillaSeccion::onlyTrashed()->find($this->itemId);
      if ($item) {
         $item->restore();
         $this->dispatch('notify', type: 'success', message: 'Sección restaurada correctamente.');
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
      $item = PlantillaSeccion::onlyTrashed()->find($this->itemId);
      if ($item) {
         $item->forceDelete();
         $this->dispatch('notify', type: 'success', message: 'Sección eliminada permanentemente.');
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
      $this->resetForm();
   }

   private function resetForm(): void
   {
      $this->reset([
         'itemId',
         'codigo',
         'nombre',
         'descripcion',
         'contenido_html',
         'categoria',
         'aplicable_a',
         'es_obligatoria',
         'estado',
         'orden',
      ]);
      $this->estado = '1';
      $this->resetValidation();
   }

   public function render()
   {
      return view('hr::livewire.seccion-manager');
   }
}
