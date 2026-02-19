<?php

namespace Modules\HR\Livewire;

use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\HR\Models\TipoDocumento;

class TipoDocumentoManager extends Component
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
   public string $categoria = '';
   public string $descripcion = '';
   public bool $requiere_firma_empleado = false;
   public bool $requiere_firma_empleador = true;
   public bool $requiere_testigos = false;
   public bool $requiere_notarizacion = false;
   public bool $usa_numeracion_automatica = true;
   public string $prefijo_numeracion = '';
   public string $formato_numeracion = '{prefijo}{año}-{numero:4}';
   public bool $estado = true;
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
   public function tipos()
   {
      $query = TipoDocumento::query()
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
            $q->where('estado', $this->statusFilter === '1');
         })
         ->orderBy($this->sortField, $this->sortDirection);

      if ($this->showTrashed) {
         $query->onlyTrashed();
      }

      return $query->paginate($this->perPage);
   }

   protected function rules(): array
   {
      return [
         'codigo' => [
            'required',
            'string',
            'max:20',
            Rule::unique('hr_tipos_documento', 'codigo')->ignore($this->itemId),
         ],
         'nombre' => ['required', 'string', 'max:100'],
         'categoria' => ['required', 'string', 'in:contractual,certificacion,administrativo,disciplinario,liquidacion'],
         'descripcion' => ['nullable', 'string'],
         'requiere_firma_empleado' => ['boolean'],
         'requiere_firma_empleador' => ['boolean'],
         'requiere_testigos' => ['boolean'],
         'requiere_notarizacion' => ['boolean'],
         'usa_numeracion_automatica' => ['boolean'],
         'prefijo_numeracion' => ['nullable', 'string', 'max:10'],
         'formato_numeracion' => ['nullable', 'string', 'max:50'],
         'estado' => ['boolean'],
         'orden' => ['integer', 'min:0'],
      ];
   }

   protected function messages(): array
   {
      return [
         'codigo.required' => 'El código es obligatorio.',
         'codigo.unique' => 'Este código ya existe.',
         'nombre.required' => 'El nombre es obligatorio.',
         'categoria.required' => 'La categoría es obligatoria.',
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

   #[On('openCreateTipoModal')]
   public function create(): void
   {
      $this->resetForm();
      $this->isEditing = false;
      $this->showModal = true;
   }

   public function edit(int $id): void
   {
      $item = TipoDocumento::findOrFail($id);

      $this->itemId = $item->id;
      $this->codigo = $item->codigo;
      $this->nombre = $item->nombre;
      $this->categoria = $item->categoria;
      $this->descripcion = $item->descripcion ?? '';
      $this->requiere_firma_empleado = (bool) $item->requiere_firma_empleado;
      $this->requiere_firma_empleador = (bool) $item->requiere_firma_empleador;
      $this->requiere_testigos = (bool) $item->requiere_testigos;
      $this->requiere_notarizacion = (bool) $item->requiere_notarizacion;
      $this->usa_numeracion_automatica = (bool) $item->usa_numeracion_automatica;
      $this->prefijo_numeracion = $item->prefijo_numeracion ?? '';
      $this->formato_numeracion = $item->formato_numeracion ?? '{prefijo}{año}-{numero:4}';
      $this->estado = (bool) $item->estado;
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
         'categoria' => $this->categoria,
         'descripcion' => $this->descripcion ?: null,
         'requiere_firma_empleado' => $this->requiere_firma_empleado,
         'requiere_firma_empleador' => $this->requiere_firma_empleador,
         'requiere_testigos' => $this->requiere_testigos,
         'requiere_notarizacion' => $this->requiere_notarizacion,
         'usa_numeracion_automatica' => $this->usa_numeracion_automatica,
         'prefijo_numeracion' => $this->prefijo_numeracion ?: null,
         'formato_numeracion' => $this->formato_numeracion ?: null,
         'estado' => $this->estado,
         'orden' => $this->orden,
      ];

      if ($this->isEditing) {
         $item = TipoDocumento::findOrFail($this->itemId);
         $item->update($data);
         $message = __('Tipo de documento actualizado correctamente.');
      } else {
         TipoDocumento::create($data);
         $message = __('Tipo de documento creado correctamente.');
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
      TipoDocumento::findOrFail($this->itemId)->delete();
      $this->showDeleteModal = false;
      $this->itemId = null;
      $this->dispatch('notify', type: 'success', message: 'Tipo de documento eliminado correctamente.');
   }

   public function toggleEstado(int $id): void
   {
      $item = TipoDocumento::findOrFail($id);
      $item->update(['1' => !$item->estado]);
      $mensaje = $item->estado ? 'Tipo activado correctamente.' : 'Tipo desactivado correctamente.';
      $this->dispatch('notify', type: 'success', message: $mensaje);
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
      $item = TipoDocumento::onlyTrashed()->find($this->itemId);
      if ($item) {
         $item->restore();
         $this->dispatch('notify', type: 'success', message: 'Tipo restaurado correctamente.');
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
      $item = TipoDocumento::onlyTrashed()->find($this->itemId);
      if ($item) {
         $item->forceDelete();
         $this->dispatch('notify', type: 'success', message: 'Tipo eliminado permanentemente.');
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
         'categoria',
         'descripcion',
         'requiere_firma_empleado',
         'requiere_firma_empleador',
         'requiere_testigos',
         'requiere_notarizacion',
         'usa_numeracion_automatica',
         'prefijo_numeracion',
         'formato_numeracion',
         'estado',
         'orden',
      ]);
      $this->requiere_firma_empleador = true;
      $this->usa_numeracion_automatica = true;
      $this->estado = true;
      $this->formato_numeracion = '{prefijo}{año}-{numero:4}';
      $this->resetValidation();
   }

   public function render()
   {
      return view('hr::livewire.tipo-documento-manager');
   }
}
