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

class CompanyManager extends Component
{
   use WithPagination;

   // ─────────────────────────────────────────────────────────────
   // Propiedades de búsqueda y filtros
   // ─────────────────────────────────────────────────────────────

   #[Url(as: 'q')]
   public string $search = '';

   #[Url]
   public string $groupFilter = '';

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

   public ?int $companyId = null;
   public string $code = '';
   public string $name = '';
   public string $trade_name = '';
   public string $tax_id = '';
   public string $timezone = '';
   public string $address = '';
   public string $phone = '';
   public string $email = '';
   public string $status = 'active';
   public int $group_company_id = 0;

   // ─────────────────────────────────────────────────────────────
   // Propiedades adicionales para modales
   // ─────────────────────────────────────────────────────────────

   public bool $showRestoreModal = false;
   public bool $showForceDeleteModal = false;
   public bool $showTrashedCompanies = false;

   // ─────────────────────────────────────────────────────────────
   // Propiedades computadas
   // ─────────────────────────────────────────────────────────────

   #[Computed]
   public function currentGroup(): ?GroupCompany
   {
      return current_group();
   }

   #[Computed]
   public function companies()
   {
      $group = $this->currentGroup;

      $query = Company::query()
         ->with('groupCompany')
         // Filtrar por tenant del grupo actual
         ->when($group, fn($q) => $q->whereHas('groupCompany', function ($q) use ($group) {
            $q->where('tenant_id', $group->tenant_id);
         }))
         ->when($this->search, fn($q) => $q->where(function ($q) {
            $q->where('name', 'like', "%{$this->search}%")
               ->orWhere('trade_name', 'like', "%{$this->search}%")
               ->orWhere('tax_id', 'like', "%{$this->search}%")
               ->orWhere('code', 'like', "%{$this->search}%");
         }))
         ->when($this->groupFilter, fn($q) => $q->where('group_company_id', $this->groupFilter))
         ->when($this->statusFilter !== '', function ($q) {
            if ($this->statusFilter === 'active') {
               $q->where('status', 'active');
            } elseif ($this->statusFilter === 'inactive') {
               $q->where('status', 'inactive');
            }
         })
         ->orderBy($this->sortField, $this->sortDirection);

      // Mostrar empresas eliminadas si está activada la opción
      if ($this->showTrashedCompanies) {
         $query->onlyTrashed();
      }

      return $query->paginate($this->perPage);
   }

   #[Computed]
   public function groups()
   {
      $group = $this->currentGroup;

      if (!$group) {
         return collect();
      }

      return GroupCompany::where('tenant_id', $group->tenant_id)
         ->active()
         ->orderBy('code')
         ->get();
   }

   // ─────────────────────────────────────────────────────────────
   // Reglas de validación
   // ─────────────────────────────────────────────────────────────

   protected function rules(): array
   {
      return [
         'code' => ['nullable', 'string', 'max:20'],
         'name' => ['required', 'string', 'min:3', 'max:150'],
         'trade_name' => ['nullable', 'string', 'max:150'],
         'tax_id' => ['nullable', 'string', 'max:20'],
         'timezone' => ['nullable', 'string', 'max:50'],
         'address' => ['nullable', 'string'],
         'phone' => ['nullable', 'string', 'max:50'],
         'email' => ['nullable', 'email', 'max:100'],
         'status' => ['required', 'string', 'in:active,inactive'],
         'group_company_id' => ['required', 'exists:core_group_companies,id'],
      ];
   }

   protected function messages(): array
   {
      return [
         'name.required' => 'El nombre de la empresa es obligatorio.',
         'name.min' => 'El nombre debe tener al menos 3 caracteres.',
         'email.email' => 'Ingresa un correo electrónico válido.',
         'group_company_id.required' => 'Selecciona un grupo.',
         'group_company_id.exists' => 'El grupo seleccionado no es válido.',
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

   public function updatedGroupFilter(): void
   {
      $this->resetPage();
   }

   public function clearFilters(): void
   {
      $this->reset(['search', 'groupFilter', 'statusFilter']);
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

      // Por defecto, seleccionar el grupo actual
      $group = $this->currentGroup;
      if ($group) {
         $this->group_company_id = $group->id;
      }

      $this->showModal = true;
   }

   // ─────────────────────────────────────────────────────────────
   // CRUD - Editar
   // ─────────────────────────────────────────────────────────────

   public function edit(int $id): void
   {
      $company = Company::findOrFail($id);

      $this->companyId = $company->id;
      $this->code = $company->code ?? '';
      $this->name = $company->name;
      $this->trade_name = $company->trade_name ?? '';
      $this->tax_id = $company->tax_id ?? '';
      $this->timezone = $company->timezone ?? '';
      $this->address = $company->address ?? '';
      $this->phone = $company->phone ?? '';
      $this->email = $company->email ?? '';
      $this->status = $company->status;
      $this->group_company_id = $company->group_company_id;

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
         'trade_name' => $this->trade_name ?: null,
         'tax_id' => $this->tax_id ?: null,
         'timezone' => $this->timezone ?: null,
         'address' => $this->address ?: null,
         'phone' => $this->phone ?: null,
         'email' => $this->email ?: null,
         'status' => $this->status,
         'group_company_id' => $this->group_company_id,
      ];

      if ($this->isEditing) {
         $company = Company::find($this->companyId);
         $company->update($data);
         $message = __('Empresa actualizada correctamente.');
      } else {
         Company::create($data);
         $message = __('Empresa creada correctamente.');
      }

      $this->closeModal();
      $this->dispatch('notify', type: 'success', message: $message);
   }

   // ─────────────────────────────────────────────────────────────
   // CRUD - Eliminar
   // ─────────────────────────────────────────────────────────────

   public function confirmDelete(int $id): void
   {
      $this->companyId = $id;
      $this->showDeleteModal = true;
   }

   public function delete(): void
   {
      $company = Company::find($this->companyId);
      $company->delete();

      $this->showDeleteModal = false;
      $this->companyId = null;
      $this->dispatch('notify', type: 'success', message: 'Empresa eliminada correctamente.');
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
         'companyId',
         'code',
         'name',
         'trade_name',
         'tax_id',
         'timezone',
         'address',
         'phone',
         'email',
         'status',
         'group_company_id'
      ]);
      $this->status = 'active';
      $this->resetValidation();
   }

   // ─────────────────────────────────────────────────────────────
   // Métodos de Estado (Activar/Desactivar)
   // ─────────────────────────────────────────────────────────────

   public function toggleStatus(int $id): void
   {
      $company = Company::findOrFail($id);

      $nuevoEstado = $company->status === 'active' ? 'inactive' : 'active';
      $company->update(['status' => $nuevoEstado]);

      $mensaje = $nuevoEstado === 'active'
         ? 'Empresa activada correctamente.'
         : 'Empresa desactivada correctamente.';

      $this->dispatch('notify', type: 'success', message: $mensaje);
   }

   // ─────────────────────────────────────────────────────────────
   // Métodos de SoftDeletes (Restaurar/Eliminar permanentemente)
   // ─────────────────────────────────────────────────────────────

   public function toggleTrashedCompanies(): void
   {
      $this->showTrashedCompanies = !$this->showTrashedCompanies;
      $this->resetPage();
   }

   public function confirmRestore(int $id): void
   {
      $this->companyId = $id;
      $this->showRestoreModal = true;
   }

   public function restore(): void
   {
      $company = Company::onlyTrashed()->find($this->companyId);

      if ($company) {
         $company->restore();
         $this->dispatch('notify', type: 'success', message: 'Empresa restaurada correctamente.');
      }

      $this->showRestoreModal = false;
      $this->companyId = null;
   }

   public function confirmForceDelete(int $id): void
   {
      $this->companyId = $id;
      $this->showForceDeleteModal = true;
   }

   public function forceDelete(): void
   {
      $company = Company::onlyTrashed()->find($this->companyId);

      if ($company) {
         $company->forceDelete();
         $this->dispatch('notify', type: 'success', message: 'Empresa eliminada permanentemente.');
      }

      $this->showForceDeleteModal = false;
      $this->companyId = null;
   }

   // ─────────────────────────────────────────────────────────────
   // Render
   // ─────────────────────────────────────────────────────────────

   public function render()
   {
      return view('core::livewire.company-manager');
   }
}
