<?php

namespace Modules\Core\Livewire;

use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Core\Models\Permission;
use Modules\Core\Models\Role;

class RoleManager extends Component
{
    use WithPagination;

    // ─────────────────────────────────────────────────────────────
    // Propiedades de búsqueda y filtros
    // ─────────────────────────────────────────────────────────────
    
    #[Url(as: 'q')]
    public string $search = '';
    
    #[Url]
    public string $systemFilter = '';
    
    #[Url]
    public string $moduleFilter = '';
    
    public string $sortField = 'display_name';
    public string $sortDirection = 'asc';
    
    public int $perPage = 10;

    // ─────────────────────────────────────────────────────────────
    // Propiedades del formulario
    // ─────────────────────────────────────────────────────────────
    
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public bool $showPermissionsModal = false;
    public bool $isEditing = false;
    
    public ?int $roleId = null;
    public string $name = '';
    public string $display_name = '';
    public string $description = '';
    public bool $is_system = false;
    
    // Permisos seleccionados
    public array $selectedPermissions = [];

    // ─────────────────────────────────────────────────────────────
    // Propiedades computadas
    // ─────────────────────────────────────────────────────────────
    
    #[Computed]
    public function roles()
    {
        return Role::query()
            ->withCount(['permissions', 'users'])
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('display_name', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%");
            }))
            ->when($this->systemFilter !== '', fn ($q) => $q->where('is_system', $this->systemFilter === 'system'))
            ->when($this->moduleFilter, fn ($q) => $q->whereHas('permissions', function ($q) {
                $q->where('module', $this->moduleFilter);
            }))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    #[Computed]
    public function permissions()
    {
        return Permission::orderBy('module')->orderBy('name')->get();
    }

    #[Computed]
    public function permissionsByModule()
    {
        return Permission::orderBy('name')->get()->groupBy('module');
    }

    #[Computed]
    public function modules()
    {
        return Permission::select('module')->distinct()->orderBy('module')->pluck('module');
    }

    // ─────────────────────────────────────────────────────────────
    // Reglas de validación
    // ─────────────────────────────────────────────────────────────
    
    protected function rules(): array
    {
        return [
            'name' => [
                'required', 
                'string', 
                'min:2', 
                'max:50',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique('core_roles', 'name')->ignore($this->roleId)
            ],
            'display_name' => ['required', 'string', 'min:2', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_system' => ['boolean'],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'El identificador del rol es obligatorio.',
            'name.regex' => 'El identificador solo puede contener letras minúsculas, números y guiones.',
            'name.unique' => 'Este identificador ya está en uso.',
            'display_name.required' => 'El nombre a mostrar es obligatorio.',
            'display_name.min' => 'El nombre debe tener al menos 2 caracteres.',
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

    public function updatedSystemFilter(): void
    {
        $this->resetPage();
    }

    public function updatedModuleFilter(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'systemFilter', 'moduleFilter']);
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
        $role = Role::findOrFail($id);
        
        $this->roleId = $role->id;
        $this->name = $role->name;
        $this->display_name = $role->display_name;
        $this->description = $role->description ?? '';
        $this->is_system = $role->is_system;
        
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
            'name' => $this->name,
            'guard_name' => 'web',
            'display_name' => $this->display_name,
            'description' => $this->description ?: null,
            'is_system' => $this->is_system,
        ];

        if ($this->isEditing) {
            $role = Role::find($this->roleId);
            
            // No permitir modificar el nombre de roles del sistema
            if ($role->isSystemRole() && $role->name !== $this->name) {
                $this->dispatch('notify', type: 'error', message: 'No puedes cambiar el nombre de un rol del sistema.');
                return;
            }
            
            $role->update($data);
            $message = 'Rol actualizado correctamente.';
        } else {
            Role::create($data);
            $message = 'Rol creado correctamente.';
        }

        $this->closeModal();
        $this->dispatch('notify', type: 'success', message: $message);
    }

    // ─────────────────────────────────────────────────────────────
    // CRUD - Eliminar
    // ─────────────────────────────────────────────────────────────
    
    public function confirmDelete(int $id): void
    {
        $this->roleId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        $role = Role::find($this->roleId);
        
        // No permitir eliminar roles del sistema
        if ($role->isSystemRole()) {
            $this->dispatch('notify', type: 'error', message: 'No puedes eliminar este rol protegido del sistema.');
            $this->showDeleteModal = false;
            return;
        }

        // No permitir eliminar roles con usuarios asignados
        $usersCount = $role->users()->count();
        if ($usersCount > 0) {
            $this->dispatch('notify', type: 'error', message: "No puedes eliminar este rol porque tiene {$usersCount} usuario(s) asignado(s).");
            $this->showDeleteModal = false;
            return;
        }

        // Eliminar permisos asociados
        $role->permissions()->detach();
        $role->delete();
        
        $this->showDeleteModal = false;
        $this->roleId = null;
        $this->dispatch('notify', type: 'success', message: 'Rol eliminado correctamente.');
    }

    // ─────────────────────────────────────────────────────────────
    // Gestión de Permisos
    // ─────────────────────────────────────────────────────────────
    
    public function managePermissions(int $id): void
    {
        $role = Role::with('permissions')->findOrFail($id);
        
        $this->roleId = $role->id;
        $this->display_name = $role->display_name;
        $this->selectedPermissions = $role->permissions->pluck('id')->toArray();
        
        $this->showPermissionsModal = true;
    }

    public function savePermissions(): void
    {
        $role = Role::find($this->roleId);
        
        // No permitir modificar permisos de superadmin
        if ($role->name === 'superadmin') {
            $this->dispatch('notify', type: 'error', message: 'El rol Super Admin tiene todos los permisos por defecto.');
            $this->showPermissionsModal = false;
            return;
        }

        // Obtener los nombres de los permisos seleccionados
        $permissionNames = Permission::whereIn('id', $this->selectedPermissions)->pluck('name')->toArray();
        
        // Usar syncPermissions de Spatie
        $role->syncPermissions($permissionNames);
        
        $this->showPermissionsModal = false;
        $this->dispatch('notify', type: 'success', message: 'Permisos actualizados correctamente.');
    }

    public function toggleModulePermissions(string $module): void
    {
        $modulePermissions = Permission::where('module', $module)->pluck('id')->toArray();
        
        // Verificar si todos los permisos del módulo están seleccionados
        $allSelected = count(array_intersect($modulePermissions, $this->selectedPermissions)) === count($modulePermissions);
        
        if ($allSelected) {
            // Quitar todos los permisos del módulo
            $this->selectedPermissions = array_diff($this->selectedPermissions, $modulePermissions);
        } else {
            // Agregar todos los permisos del módulo
            $this->selectedPermissions = array_unique(array_merge($this->selectedPermissions, $modulePermissions));
        }
    }

    public function isModuleFullySelected(string $module): bool
    {
        $modulePermissions = Permission::where('module', $module)->pluck('id')->toArray();
        return count(array_intersect($modulePermissions, $this->selectedPermissions)) === count($modulePermissions);
    }

    public function isModulePartiallySelected(string $module): bool
    {
        $modulePermissions = Permission::where('module', $module)->pluck('id')->toArray();
        $intersection = array_intersect($modulePermissions, $this->selectedPermissions);
        return count($intersection) > 0 && count($intersection) < count($modulePermissions);
    }

    // ─────────────────────────────────────────────────────────────
    // Toggle Sistema
    // ─────────────────────────────────────────────────────────────
    
    public function toggleSystem(int $id): void
    {
        $role = Role::findOrFail($id);
        
        // No permitir modificar roles del sistema predefinidos
        if (in_array($role->name, Role::SYSTEM_ROLES)) {
            $this->dispatch('notify', type: 'error', message: 'No puedes modificar este rol del sistema.');
            return;
        }

        $role->update(['is_system' => !$role->is_system]);
        
        $message = $role->is_system ? 'Rol marcado como sistema.' : 'Rol desmarcado como sistema.';
        $this->dispatch('notify', type: 'success', message: $message);
    }

    // ─────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────
    
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->showDeleteModal = false;
        $this->showPermissionsModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset([
            'roleId', 'name', 'display_name', 'description', 'is_system', 'selectedPermissions'
        ]);
        $this->is_system = false;
        $this->resetValidation();
    }

    /**
     * Verificar si un rol es protegido (no se puede eliminar)
     */
    public function isProtectedRole(string $roleName): bool
    {
        return in_array($roleName, Role::SYSTEM_ROLES);
    }

    /**
     * Obtener el color del badge según el rol
     */
    public function getRoleBadgeColor(string $roleName): string
    {
        return match ($roleName) {
            'superadmin' => 'danger',
            'admin' => 'warning',
            'manager' => 'info',
            'accountant' => 'success',
            'salesperson' => 'primary',
            'hr-manager' => 'dark',
            'user' => 'secondary',
            default => 'light',
        };
    }

    // ─────────────────────────────────────────────────────────────
    // Render
    // ─────────────────────────────────────────────────────────────
    
    public function render()
    {
        return view('core::livewire.role-manager');
    }
}
