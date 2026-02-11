<?php

namespace Modules\Core\Livewire;

use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Core\Models\Company;
use Modules\Core\Models\GroupCompany;
use Modules\Core\Models\Location;

class LocationManager extends Component
{
   use WithPagination;

   // ─────────────────────────────────────────────────────────────
   // Propiedades de búsqueda y filtros
   // ─────────────────────────────────────────────────────────────

   #[Url(as: 'q')]
   public string $search = '';

   #[Url]
   public string $companyFilter = '';

   #[Url]
   public string $statusFilter = '';

   public string $sortField = 'created_at';
   public string $sortDirection = 'desc';

   public int $perPage = 10;

   // ─────────────────────────────────────────────────────────────
   // Propiedades del formulario
   // ─────────────────────────────────────────────────────────────

   public bool $showModal = false;
   public bool $showDeleteModal = false;
   public bool $isEditing = false;

   public ?int $locationId = null;
   public string $code = '';
   public string $name = '';
   public int $company_id = 0;
   public string $timezone = '';
   public string $address = '';
   public string $city = '';
   public string $phone = '';
   public bool $is_main = false;
   public string $status = 'active';

   // ─────────────────────────────────────────────────────────────
   // Propiedades adicionales para modales
   // ─────────────────────────────────────────────────────────────

   public bool $showRestoreModal = false;
   public bool $showForceDeleteModal = false;
   public bool $showTrashedLocations = false;

   // ─────────────────────────────────────────────────────────────
   // Propiedades computadas
   // ─────────────────────────────────────────────────────────────

   #[Computed]
   public function currentGroup(): ?GroupCompany
   {
      return current_group();
   }

   #[Computed]
   public function locations()
   {
      $group = $this->currentGroup;

      $query = Location::query()
         ->with(['company.groupCompany'])
         // Filtrar por tenant del grupo actual
         ->when($group, fn($q) => $q->whereHas('company.groupCompany', function ($q) use ($group) {
            $q->where('tenant_id', $group->tenant_id);
         }))
         ->when($this->search, fn($q) => $q->where(function ($q) {
            $q->where('name', 'like', "%{$this->search}%")
               ->orWhere('city', 'like', "%{$this->search}%")
               ->orWhere('code', 'like', "%{$this->search}%");
         }))
         ->when($this->companyFilter, fn($q) => $q->where('company_id', $this->companyFilter))
         ->when($this->statusFilter !== '', function ($q) {
            if ($this->statusFilter === 'active') {
               $q->where('status', 'active');
            } elseif ($this->statusFilter === 'inactive') {
               $q->where('status', 'inactive');
            }
         })
         ->orderBy($this->sortField, $this->sortDirection);

      // Mostrar locales eliminados si está activada la opción
      if ($this->showTrashedLocations) {
         $query->onlyTrashed();
      }

      return $query->paginate($this->perPage);
   }

   #[Computed]
   public function companies()
   {
      $group = $this->currentGroup;

      if (!$group) {
         return collect();
      }

      return Company::whereHas('groupCompany', function ($q) use ($group) {
         $q->where('tenant_id', $group->tenant_id);
      })
         ->active()
         ->orderBy('name')
         ->get();
   }

   // ─────────────────────────────────────────────────────────────
   // Reglas de validación
   // ─────────────────────────────────────────────────────────────

   protected function rules(): array
   {
      return [
         'code' => ['nullable', 'string', 'max:20'],
         'name' => ['required', 'string', 'min:3', 'max:100'],
         'company_id' => ['required', 'exists:core_companies,id'],
         'timezone' => ['nullable', 'string', 'max:50'],
         'address' => ['nullable', 'string'],
         'city' => ['nullable', 'string', 'max:100'],
         'phone' => ['nullable', 'string', 'max:50'],
         'is_main' => ['boolean'],
         'status' => ['required', 'string', 'in:active,inactive'],
      ];
   }

   protected function messages(): array
   {
      return [
         'name.required' => 'El nombre del local es obligatorio.',
         'name.min' => 'El nombre debe tener al menos 3 caracteres.',
         'company_id.required' => 'Selecciona una empresa.',
         'company_id.exists' => 'La empresa seleccionada no es válida.',
      ];
   }

   // ─────────────────────────────────────────────────────────────
   // Métodos de ordenamiento y filtros
   // ─────────────────────────────────────────────────────────────

   public function sortBy(string $field): void
   {
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

   public function updatedCompanyFilter(): void
   {
      $this->resetPage();
   }

   public function clearFilters(): void
   {
      $this->reset(['search', 'companyFilter', 'statusFilter']);
      $this->resetPage();
   }

   // ─────────────────────────────────────────────────────────────
   // CRUD - Crear
   // ─────────────────────────────────────────────────────────────

   #[On('openCreateModal')]
   public function create(): void
   {
      $this->resetForm();
      $this->isEditing = false;
      $this->showModal = true;
   }

   // ─────────────────────────────────────────────────────────────
   // CRUD - Editar
   // ─────────────────────────────────────────────────────────────

   public function edit(int $id): void
   {
      $location = Location::findOrFail($id);

      $this->locationId = $location->id;
      $this->code = $location->code ?? '';
      $this->name = $location->name;
      $this->company_id = $location->company_id;
      $this->timezone = $location->timezone ?? '';
      $this->address = $location->address ?? '';
      $this->city = $location->city ?? '';
      $this->phone = $location->phone ?? '';
      $this->is_main = $location->is_main;
      $this->status = $location->status;

      $this->isEditing = true;
      $this->showModal = true;
   }

   // ─────────────────────────────────────────────────────────────
   // CRUD - Guardar (crear o actualizar)
   // ─────────────────────────────────────────────────────────────

   public function save(): void
   {
      $this->validate();

      $data = [
         'code' => $this->code ?: null,
         'name' => $this->name,
         'company_id' => $this->company_id,
         'timezone' => $this->timezone ?: null,
         'address' => $this->address ?: null,
         'city' => $this->city ?: null,
         'phone' => $this->phone ?: null,
         'is_main' => $this->is_main,
         'status' => $this->status,
      ];

      if ($this->isEditing) {
         $location = Location::find($this->locationId);
         $location->update($data);

         // Si se marcó como principal, actualizar otros locales
         if ($this->is_main) {
            $location->setAsMain();
         }

         $message = __('Local actualizado correctamente.');
      } else {
         $location = Location::create($data);

         // Si se marcó como principal, actualizar otros locales
         if ($this->is_main) {
            $location->setAsMain();
         }

         $message = __('Local creado correctamente.');
      }

      $this->closeModal();
      $this->dispatch('notify', type: 'success', message: $message);
   }

   // ─────────────────────────────────────────────────────────────
   // CRUD - Eliminar
   // ─────────────────────────────────────────────────────────────

   public function confirmDelete(int $id): void
   {
      $this->locationId = $id;
      $this->showDeleteModal = true;
   }

   public function delete(): void
   {
      $location = Location::find($this->locationId);
      $location->delete();

      $this->showDeleteModal = false;
      $this->locationId = null;
      $this->dispatch('notify', type: 'success', message: 'Local eliminado correctamente.');
   }

   // ─────────────────────────────────────────────────────────────
   // Helpers
   // ─────────────────────────────────────────────────────────────

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
         'locationId',
         'code',
         'name',
         'company_id',
         'timezone',
         'address',
         'city',
         'phone',
         'is_main',
         'status'
      ]);
      $this->status = 'active';
      $this->is_main = false;
      $this->resetValidation();
   }

   // ─────────────────────────────────────────────────────────────
   // Métodos de Estado (Activar/Desactivar)
   // ─────────────────────────────────────────────────────────────

   public function toggleStatus(int $id): void
   {
      $location = Location::findOrFail($id);

      $nuevoEstado = $location->status === 'active' ? 'inactive' : 'active';
      $location->update(['status' => $nuevoEstado]);

      $mensaje = $nuevoEstado === 'active'
         ? 'Local activado correctamente.'
         : 'Local desactivado correctamente.';

      $this->dispatch('notify', type: 'success', message: $mensaje);
   }

   // ─────────────────────────────────────────────────────────────
   // Método para establecer como principal
   // ─────────────────────────────────────────────────────────────

   public function setAsMain(int $id): void
   {
      $location = Location::findOrFail($id);
      $location->setAsMain();

      $this->dispatch('notify', type: 'success', message: 'Local establecido como principal.');
   }

   // ─────────────────────────────────────────────────────────────
   // Métodos de SoftDeletes (Restaurar/Eliminar permanentemente)
   // ─────────────────────────────────────────────────────────────

   public function toggleTrashedLocations(): void
   {
      $this->showTrashedLocations = !$this->showTrashedLocations;
      $this->resetPage();
   }

   public function confirmRestore(int $id): void
   {
      $this->locationId = $id;
      $this->showRestoreModal = true;
   }

   public function restore(): void
   {
      $location = Location::onlyTrashed()->find($this->locationId);

      if ($location) {
         $location->restore();
         $this->dispatch('notify', type: 'success', message: 'Local restaurado correctamente.');
      }

      $this->showRestoreModal = false;
      $this->locationId = null;
   }

   public function confirmForceDelete(int $id): void
   {
      $this->locationId = $id;
      $this->showForceDeleteModal = true;
   }

   public function forceDelete(): void
   {
      $location = Location::onlyTrashed()->find($this->locationId);

      if ($location) {
         $location->forceDelete();
         $this->dispatch('notify', type: 'success', message: 'Local eliminado permanentemente.');
      }

      $this->showForceDeleteModal = false;
      $this->locationId = null;
   }

   // ─────────────────────────────────────────────────────────────
   // Render
   // ─────────────────────────────────────────────────────────────

   public function render()
   {
      return view('core::livewire.location-manager');
   }
}
