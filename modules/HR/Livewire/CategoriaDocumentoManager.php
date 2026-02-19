<?php

namespace Modules\HR\Livewire;

use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\HR\Models\CategoriaDocumento;
use Modules\HR\Models\TipoDocumento;

class CategoriaDocumentoManager extends Component
{
   use WithPagination;

   #[Url(as: 'q')]
   public string $search = '';

   #[Url]
   public string $tipoFilter = '';

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
   public string $tipo_documento_id = '';
   public string $codigo = '';
   public string $nombre = '';
   public string $descripcion = '';
   public bool $requiere_justificacion = false;
   public bool $requiere_aprobacion = false;
   public string $nivel_aprobacion = '';
   public string $articulo_ley = '';
   public string $estado = '1';
   public int $orden = 0;

   protected array $allowedSortFields = [
      'id',
      'codigo',
      'nombre',
      'tipo_documento_id',
      'estado',
      'orden',
      'created_at',
   ];

   #[Computed]
   public function categorias()
   {
      $query = CategoriaDocumento::query()
         ->with('tipoDocumento')
         ->when($this->search, function ($q) {
            $q->where(function ($q) {
               $q->where('nombre', 'like', "%{$this->search}%")
                  ->orWhere('codigo', 'like', "%{$this->search}%")
                  ->orWhere('descripcion', 'like', "%{$this->search}%");
            });
         })
         ->when($this->tipoFilter !== '', function ($q) {
            $q->where('tipo_documento_id', $this->tipoFilter);
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
      return TipoDocumento::active()->orderBy('orden')->get();
   }

   protected function rules(): array
   {
      return [
         'tipo_documento_id' => ['required', 'exists:hr_tipos_documento,id'],
         'codigo' => [
            'required',
            'string',
            'max:20',
            Rule::unique('hr_categorias_documento', 'codigo')->ignore($this->itemId),
         ],
         'nombre' => ['required', 'string', 'max:200'],
         'descripcion' => ['nullable', 'string'],
         'requiere_justificacion' => ['boolean'],
         'requiere_aprobacion' => ['boolean'],
         'nivel_aprobacion' => ['nullable', 'string', 'max:50'],
         'articulo_ley' => ['nullable', 'string', 'max:50'],
         'estado' => ['required', 'in:1,0'],
         'orden' => ['integer', 'min:0'],
      ];
   }

   protected function messages(): array
   {
      return [
         'tipo_documento_id.required' => 'El tipo de documento es obligatorio.',
         'codigo.required' => 'El código es obligatorio.',
         'codigo.unique' => 'Este código ya existe.',
         'nombre.required' => 'El nombre es obligatorio.',
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
      $this->reset(['search', 'tipoFilter', 'statusFilter']);
      $this->resetPage();
   }

   #[On('openCreateCategoriaModal')]
   public function create(): void
   {
      $this->resetForm();
      $this->isEditing = false;
      $this->showModal = true;
   }

   public function edit(int $id): void
   {
      $item = CategoriaDocumento::findOrFail($id);

      $this->itemId = $item->id;
      $this->tipo_documento_id = (string) $item->tipo_documento_id;
      $this->codigo = $item->codigo;
      $this->nombre = $item->nombre;
      $this->descripcion = $item->descripcion ?? '';
      $this->requiere_justificacion = (bool) $item->requiere_justificacion;
      $this->requiere_aprobacion = (bool) $item->requiere_aprobacion;
      $this->nivel_aprobacion = $item->nivel_aprobacion ?? '';
      $this->articulo_ley = $item->articulo_ley ?? '';
      $this->estado = $item->estado ?? '1';
      $this->orden = (int) $item->orden;

      $this->isEditing = true;
      $this->showModal = true;
   }

   public function save(): void
   {
      $this->validate();

      $data = [
         'tipo_documento_id' => $this->tipo_documento_id,
         'codigo' => $this->codigo,
         'nombre' => $this->nombre,
         'descripcion' => $this->descripcion ?: null,
         'requiere_justificacion' => $this->requiere_justificacion,
         'requiere_aprobacion' => $this->requiere_aprobacion,
         'nivel_aprobacion' => $this->nivel_aprobacion ?: null,
         'articulo_ley' => $this->articulo_ley ?: null,
         'estado' => $this->estado,
         'orden' => $this->orden,
      ];

      if ($this->isEditing) {
         $item = CategoriaDocumento::findOrFail($this->itemId);
         $item->update($data);
         $message = __('Categoría actualizada correctamente.');
      } else {
         CategoriaDocumento::create($data);
         $message = __('Categoría creada correctamente.');
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
      CategoriaDocumento::findOrFail($this->itemId)->delete();
      $this->showDeleteModal = false;
      $this->itemId = null;
      $this->dispatch('notify', type: 'success', message: 'Categoría eliminada correctamente.');
   }

   public function toggleEstado(int $id): void
   {
      $item = CategoriaDocumento::findOrFail($id);
      $newEstado = $item->estado === '1' ? '0' : '1';
      $item->update(['estado' => $newEstado]);
      $this->dispatch('notify', type: 'success', message: "Categoría actualizada correctamente.");
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
      $item = CategoriaDocumento::onlyTrashed()->find($this->itemId);
      if ($item) {
         $item->restore();
         $this->dispatch('notify', type: 'success', message: 'Categoría restaurada correctamente.');
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
      $item = CategoriaDocumento::onlyTrashed()->find($this->itemId);
      if ($item) {
         $item->forceDelete();
         $this->dispatch('notify', type: 'success', message: 'Categoría eliminada permanentemente.');
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
         'tipo_documento_id',
         'codigo',
         'nombre',
         'descripcion',
         'requiere_justificacion',
         'requiere_aprobacion',
         'nivel_aprobacion',
         'articulo_ley',
         'estado',
         'orden',
      ]);
      $this->estado = '1';
      $this->resetValidation();
   }

   public function render()
   {
      return view('hr::livewire.categoria-documento-manager');
   }
}
