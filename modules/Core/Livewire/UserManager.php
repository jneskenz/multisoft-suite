<?php

namespace Modules\Core\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Core\Models\GroupCompany;
use Modules\Core\Models\Role;

class UserManager extends Component
{
    use WithPagination;

    // ─────────────────────────────────────────────────────────────
    // Propiedades de búsqueda y filtros
    // ─────────────────────────────────────────────────────────────
    
    #[Url(as: 'q')]
    public string $search = '';
    
    #[Url]
    public string $roleFilter = '';
    
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
    
    public ?int $userId = null;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $selectedRole = '';
    public int $estado = 1; // 1 = activo, 0 = suspendido
    public array $selectedGroups = []; // Grupos a los que tendrá acceso

    // ─────────────────────────────────────────────────────────────
    // Propiedades adicionales para modales
    // ─────────────────────────────────────────────────────────────
    
    public bool $showRestoreModal = false;
    public bool $showForceDeleteModal = false;
    public bool $showTrashedUsers = false;

    // ─────────────────────────────────────────────────────────────
    // Propiedades computadas
    // ─────────────────────────────────────────────────────────────
    
    #[Computed]
    public function currentGroup(): ?GroupCompany
    {
        return current_group();
    }

    #[Computed]
    public function users()
    {
        $group = $this->currentGroup;
        
        $query = User::query()
            ->with('roles')
            // Filtrar por tenant del grupo actual
            ->when($group, fn ($q) => $q->where('tenant_id', $group->tenant_id))
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            }))
            ->when($this->roleFilter, fn ($q) => $q->role($this->roleFilter))
            ->when($this->statusFilter !== '', function ($q) {
                if ($this->statusFilter === 'active') {
                    $q->where('estado', User::ESTADO_ACTIVO);
                } elseif ($this->statusFilter === 'suspended') {
                    $q->where('estado', User::ESTADO_SUSPENDIDO);
                }
            })
            ->orderBy($this->sortField, $this->sortDirection);
        
        // Mostrar usuarios eliminados si está activada la opción
        if ($this->showTrashedUsers) {
            $query->onlyTrashed();
        }
        
        return $query->paginate($this->perPage);
    }

    #[Computed]
    public function roles()
    {
        return Role::orderBy('display_name')->get();
    }

    /**
     * Grupos disponibles del tenant actual.
     * Solo muestra grupos del mismo tenant que el grupo activo.
     */
    #[Computed]
    public function availableGroups()
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
        $rules = [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => [
                'required', 
                'email', 
                Rule::unique('users', 'email')->ignore($this->userId)
            ],
            'selectedRole' => ['required', 'exists:core_roles,name'],
            'estado' => ['required', 'integer', 'in:0,1'],
            'selectedGroups' => ['required', 'array', 'min:1'],
            'selectedGroups.*' => ['exists:core_group_companies,id'],
        ];

        // Password requerido solo al crear
        if (!$this->isEditing) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        } else {
            $rules['password'] = ['nullable', 'string', 'min:8', 'confirmed'];
        }

        return $rules;
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'email.unique' => 'Este correo ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'selectedRole.required' => 'Selecciona un rol.',
            'selectedRole.exists' => 'El rol seleccionado no es válido.',
            'selectedGroups.required' => 'Selecciona al menos un grupo.',
            'selectedGroups.min' => 'Debes seleccionar al menos un grupo.',
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

    public function updatedRoleFilter(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'roleFilter', 'statusFilter']);
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
            $this->selectedGroups = [$group->id];
        }
        
        $this->showModal = true;
    }

    // ─────────────────────────────────────────────────────────────
    // CRUD - Editar
    // ─────────────────────────────────────────────────────────────
    
    public function edit(int $id): void
    {
        $user = User::findOrFail($id);
        
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->selectedRole = $user->getRoleNames()->first() ?? '';
        $this->estado = $user->estado ?? User::ESTADO_ACTIVO;
        $this->password = '';
        $this->password_confirmation = '';
        
        // Cargar grupos asignados al usuario
        $this->selectedGroups = $user->getGroupAccessIds();
        
        $this->isEditing = true;
        $this->showModal = true;
    }

    // ─────────────────────────────────────────────────────────────
    // CRUD - Guardar (crear o actualizar)
    // ─────────────────────────────────────────────────────────────
    
    public function save(): void
    {
        $this->validate();

        $group = $this->currentGroup;
        
        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'estado' => $this->estado,
        ];

        // Solo incluir password si se proporcionó
        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->isEditing) {
            $user = User::find($this->userId);
            $user->update($data);
            // Sincronizar rol con Spatie
            $user->syncRoles([$this->selectedRole]);
            // Sincronizar acceso a grupos
            $user->syncGroupAccess($this->selectedGroups);
            $message = __('Usuario actualizado correctamente.');
        } else {
            // Asignar tenant_id del grupo actual al crear usuario
            if ($group) {
                $data['tenant_id'] = $group->tenant_id;
            }
            $user = User::create($data);
            // Asignar rol con Spatie
            $user->assignRole($this->selectedRole);
            // Asignar acceso a grupos seleccionados
            $user->syncGroupAccess($this->selectedGroups);
            $message = __('Usuario creado correctamente.');
        }

        $this->closeModal();
        $this->dispatch('notify', type: 'success', message: $message);
    }

    // ─────────────────────────────────────────────────────────────
    // CRUD - Eliminar
    // ─────────────────────────────────────────────────────────────
    
    public function confirmDelete(int $id): void
    {
        $this->userId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        $user = User::find($this->userId);
        
        // No permitir eliminar al usuario actual
        if ($user->id === auth()->id()) {
            $this->dispatch('notify', type: 'error', message: 'No puedes eliminar tu propia cuenta.');
            $this->showDeleteModal = false;
            return;
        }

        // No permitir eliminar super-admins si no eres super-admin
        if ($user->isSuperAdmin() && !auth()->user()->isSuperAdmin()) {
            $this->dispatch('notify', type: 'error', message: 'No tienes permisos para eliminar este usuario.');
            $this->showDeleteModal = false;
            return;
        }

        $user->delete();
        
        $this->showDeleteModal = false;
        $this->userId = null;
        $this->dispatch('notify', type: 'success', message: 'Usuario eliminado correctamente.');
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
            'userId', 'name', 'email', 'password', 
            'password_confirmation', 'selectedRole', 'estado', 'selectedGroups'
        ]);
        $this->estado = User::ESTADO_ACTIVO;
        $this->selectedGroups = [];
        $this->resetValidation();
    }

    // ─────────────────────────────────────────────────────────────
    // Métodos de Estado (Suspender/Activar)
    // ─────────────────────────────────────────────────────────────
    
    /**
     * Cambiar el estado del usuario (suspender/activar)
     */
    public function toggleEstado(int $id): void
    {
        $user = User::findOrFail($id);
        
        // No permitir suspender al usuario actual
        if ($user->id === auth()->id()) {
            $this->dispatch('notify', type: 'error', message: 'No puedes suspender tu propia cuenta.');
            return;
        }

        // No permitir suspender super-admins si no eres super-admin
        if ($user->isSuperAdmin() && !auth()->user()->isSuperAdmin()) {
            $this->dispatch('notify', type: 'error', message: 'No tienes permisos para modificar este usuario.');
            return;
        }

        $nuevoEstado = $user->estado === User::ESTADO_ACTIVO 
            ? User::ESTADO_SUSPENDIDO 
            : User::ESTADO_ACTIVO;
        
        $user->update(['estado' => $nuevoEstado]);

        $mensaje = $nuevoEstado === User::ESTADO_ACTIVO 
            ? 'Usuario activado correctamente.' 
            : 'Usuario suspendido correctamente.';
        
        $this->dispatch('notify', type: 'success', message: $mensaje);
    }

    /**
     * Suspender un usuario
     */
    public function suspend(int $id): void
    {
        $user = User::findOrFail($id);
        
        if ($user->id === auth()->id()) {
            $this->dispatch('notify', type: 'error', message: 'No puedes suspender tu propia cuenta.');
            return;
        }

        if ($user->isSuperAdmin() && !auth()->user()->isSuperAdmin()) {
            $this->dispatch('notify', type: 'error', message: 'No tienes permisos para suspender este usuario.');
            return;
        }

        $user->update(['estado' => User::ESTADO_SUSPENDIDO]);
        $this->dispatch('notify', type: 'success', message: 'Usuario suspendido correctamente.');
    }

    /**
     * Activar un usuario
     */
    public function activate(int $id): void
    {
        $user = User::findOrFail($id);
        
        $user->update(['estado' => User::ESTADO_ACTIVO]);
        $this->dispatch('notify', type: 'success', message: 'Usuario activado correctamente.');
    }

    // ─────────────────────────────────────────────────────────────
    // Métodos de SoftDeletes (Restaurar/Eliminar permanentemente)
    // ─────────────────────────────────────────────────────────────
    
    /**
     * Mostrar/ocultar usuarios eliminados
     */
    public function toggleTrashedUsers(): void
    {
        $this->showTrashedUsers = !$this->showTrashedUsers;
        $this->resetPage();
    }

    /**
     * Confirmar restauración de usuario
     */
    public function confirmRestore(int $id): void
    {
        $this->userId = $id;
        $this->showRestoreModal = true;
    }

    /**
     * Restaurar un usuario eliminado
     */
    public function restore(): void
    {
        $user = User::onlyTrashed()->find($this->userId);
        
        if ($user) {
            $user->restore();
            $this->dispatch('notify', type: 'success', message: 'Usuario restaurado correctamente.');
        }
        
        $this->showRestoreModal = false;
        $this->userId = null;
    }

    /**
     * Confirmar eliminación permanente
     */
    public function confirmForceDelete(int $id): void
    {
        $this->userId = $id;
        $this->showForceDeleteModal = true;
    }

    /**
     * Eliminar permanentemente un usuario
     */
    public function forceDelete(): void
    {
        $user = User::onlyTrashed()->find($this->userId);
        
        if ($user) {
            $user->forceDelete();
            $this->dispatch('notify', type: 'success', message: 'Usuario eliminado permanentemente.');
        }
        
        $this->showForceDeleteModal = false;
        $this->userId = null;
    }

    // ─────────────────────────────────────────────────────────────
    // Render
    // ─────────────────────────────────────────────────────────────
    
    public function render()
    {
        return view('core::livewire.user-manager');
    }
}
