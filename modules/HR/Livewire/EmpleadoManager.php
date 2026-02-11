<?php

namespace Modules\HR\Livewire;

use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Core\Models\GroupCompany;
use Modules\HR\Models\Empleado;

class EmpleadoManager extends Component
{
    use WithPagination;

    // Propiedades de búsqueda y filtros
    #[Url(as: 'q')]
    public string $search = '';
    #[Url]
    public string $statusFilter = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 10;

    // Propiedades del formulario
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public bool $isEditing = false;
    public ?int $empleadoId = null;
    public string $nombre = '';
    public string $email = '';
    public int $estado = 1;
    public string $documento_tipo = '';
    public string $documento_numero = '';
    public string $telefono = '';
    public string $codigo_empleado = '';
    public string $cargo = '';
    public string $fecha_ingreso = '';
    public ?int $company_id = null;
    public ?int $location_id = null;

    // Propiedades adicionales para modales
    public bool $showRestoreModal = false;
    public bool $showForceDeleteModal = false;
    public bool $showTrashedEmpleados = false;

    // Propiedades computadas
    #[Computed]
    public function currentGroup(): ?GroupCompany
    {
        return current_group();
    }

    #[Computed]
    public function empleados()
    {
        $group = $this->currentGroup;
        $query = Empleado::query()
            ->with(['company', 'location', 'user'])
            ->when($group, fn ($q) => $q->where('group_company_id', $group->id))
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('nombre', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
                  ->orWhere('documento_numero', 'like', "%{$this->search}%")
                  ->orWhere('codigo_empleado', 'like', "%{$this->search}%");
            }))
            ->when($this->statusFilter !== '', function ($q) {
                if ($this->statusFilter === 'active') {
                    $q->where('estado', Empleado::ESTADO_ACTIVO);
                } elseif ($this->statusFilter === 'suspended') {
                    $q->where('estado', Empleado::ESTADO_SUSPENDIDO);
                } elseif ($this->statusFilter === 'cesado') {
                    $q->where('estado', Empleado::ESTADO_CESADO);
                }
            })
            ->orderBy($this->sortField, $this->sortDirection);
        if ($this->showTrashedEmpleados) {
            $query->onlyTrashed();
        }
        return $query->paginate($this->perPage);
    }

    #[Computed]
    public function availableCompanies()
    {
        $group = $this->currentGroup;
        if (!$group) {
            return collect();
        }
        return Company::where('group_company_id', $group->id)
            ->active()
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function availableLocations()
    {
        if (!$this->company_id) {
            return collect();
        }
        return Location::where('company_id', $this->company_id)
            ->active()
            ->orderBy('name')
            ->get();
    }

    // Reglas de validación
    protected function rules(): array
    {
        $rules = [
            'nombre' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'estado' => ['required', 'integer', 'in:0,1,2'],
            'documento_tipo' => ['nullable', 'string', 'max:50'],
            'documento_numero' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('hr_empleados', 'documento_numero')->ignore($this->empleadoId)
            ],
            'telefono' => ['nullable', 'string', 'max:50'],
            'codigo_empleado' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('hr_empleados', 'codigo_empleado')->ignore($this->empleadoId)
            ],
            'cargo' => ['nullable', 'string', 'max:255'],
            'fecha_ingreso' => ['nullable', 'date'],
            'company_id' => ['required', 'exists:core_companies,id'],
            'location_id' => ['nullable', 'exists:core_locations,id'],
        ];
        return $rules;
    }

    protected function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.min' => 'El nombre debe tener al menos 3 caracteres.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'documento_numero.unique' => 'Este número de documento ya está registrado.',
            'codigo_empleado.unique' => 'Este código de empleado ya está registrado.',
            'company_id.required' => 'La empresa es obligatoria.',
            'company_id.exists' => 'La empresa seleccionada no es válida.',
            'location_id.exists' => 'El local seleccionado no es válido.',
            'fecha_ingreso.date' => 'La fecha de ingreso no es válida.',
        ];
    }

    // Métodos de ordenamiento y filtros
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

    public function clearFilters(): void
    {
        $this->reset(['search', 'statusFilter']);
        $this->resetPage();
    }

    public function updatedCompanyId(): void
    {
        // Resetear location_id cuando cambia la empresa
        $this->location_id = null;
    }

    // CRUD - Crear
    #[On('openCreateModal')]
    public function create(): void
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->showModal = true;
    }

    // CRUD - Editar
    public function edit(int $id): void
    {
        $empleado = Empleado::findOrFail($id);
        $this->empleadoId = $empleado->id;
        $this->nombre = $empleado->nombre;
        $this->email = $empleado->email ?? '';
        $this->estado = $empleado->estado ?? Empleado::ESTADO_ACTIVO;
        $this->documento_tipo = $empleado->documento_tipo ?? '';
        $this->documento_numero = $empleado->documento_numero ?? '';
        $this->telefono = $empleado->telefono ?? '';
        $this->codigo_empleado = $empleado->codigo_empleado ?? '';
        $this->cargo = $empleado->cargo ?? '';
        $this->fecha_ingreso = $empleado->fecha_ingreso?->format('Y-m-d') ?? '';
        $this->company_id = $empleado->company_id;
        $this->location_id = $empleado->location_id;
        $this->isEditing = true;
        $this->showModal = true;
    }

    // CRUD - Guardar (crear o actualizar)
    public function save(): void
    {
        $this->validate();
        $group = $this->currentGroup;
        
        $data = [
            'nombre' => $this->nombre,
            'email' => $this->email ?: null,
            'estado' => $this->estado,
            'documento_tipo' => $this->documento_tipo ?: null,
            'documento_numero' => $this->documento_numero ?: null,
            'telefono' => $this->telefono ?: null,
            'codigo_empleado' => $this->codigo_empleado ?: null,
            'cargo' => $this->cargo ?: null,
            'fecha_ingreso' => $this->fecha_ingreso ?: null,
            'company_id' => $this->company_id,
            'location_id' => $this->location_id,
        ];
        
        if ($this->isEditing) {
            $empleado = Empleado::find($this->empleadoId);
            $empleado->update($data);
            $message = __('Empleado actualizado correctamente.');
        } else {
            if ($group) {
                $data['tenant_id'] = $group->tenant_id;
                $data['group_company_id'] = $group->id;
            }
            $empleado = Empleado::create($data);
            $message = __('Empleado creado correctamente.');
        }
        
        $this->closeModal();
        $this->dispatch('notify', type: 'success', message: $message);
    }

    // CRUD - Eliminar
    public function confirmDelete(int $id): void
    {
        $this->empleadoId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        $empleado = Empleado::find($this->empleadoId);
        $empleado->delete();
        $this->showDeleteModal = false;
        $this->empleadoId = null;
        $this->dispatch('notify', type: 'success', message: 'Empleado eliminado correctamente.');
    }

    // Helpers
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
            'empleadoId', 'nombre', 'email', 'estado',
            'documento_tipo', 'documento_numero', 'telefono',
            'codigo_empleado', 'cargo', 'fecha_ingreso',
            'company_id', 'location_id'
        ]);
        $this->estado = Empleado::ESTADO_ACTIVO;
        $this->resetValidation();
    }

    // Métodos de Estado (Suspender/Activar)
    public function toggleEstado(int $id): void
    {
        $empleado = Empleado::findOrFail($id);
        
        // Ciclo: Activo -> Suspendido -> Cesado -> Activo
        $nuevoEstado = match($empleado->estado) {
            Empleado::ESTADO_ACTIVO => Empleado::ESTADO_SUSPENDIDO,
            Empleado::ESTADO_SUSPENDIDO => Empleado::ESTADO_CESADO,
            Empleado::ESTADO_CESADO => Empleado::ESTADO_ACTIVO,
            default => Empleado::ESTADO_ACTIVO,
        };
        
        $empleado->update(['estado' => $nuevoEstado]);
        
        $mensaje = match($nuevoEstado) {
            Empleado::ESTADO_ACTIVO => 'Empleado activado correctamente.',
            Empleado::ESTADO_SUSPENDIDO => 'Empleado suspendido correctamente.',
            Empleado::ESTADO_CESADO => 'Empleado marcado como cesado correctamente.',
            default => 'Estado actualizado correctamente.',
        };
        
        $this->dispatch('notify', type: 'success', message: $mensaje);
    }

    public function suspend(int $id): void
    {
        $empleado = Empleado::findOrFail($id);
        $empleado->update(['estado' => Empleado::ESTADO_SUSPENDIDO]);
        $this->dispatch('notify', type: 'success', message: 'Empleado suspendido correctamente.');
    }

    public function activate(int $id): void
    {
        $empleado = Empleado::findOrFail($id);
        $empleado->update(['estado' => Empleado::ESTADO_ACTIVO]);
        $this->dispatch('notify', type: 'success', message: 'Empleado activado correctamente.');
    }

    public function cesar(int $id): void
    {
        $empleado = Empleado::findOrFail($id);
        $empleado->update(['estado' => Empleado::ESTADO_CESADO]);
        $this->dispatch('notify', type: 'success', message: 'Empleado marcado como cesado correctamente.');
    }

    // Métodos de SoftDeletes (Restaurar/Eliminar permanentemente)
    public function toggleTrashedEmpleados(): void
    {
        $this->showTrashedEmpleados = !$this->showTrashedEmpleados;
        $this->resetPage();
    }

    public function confirmRestore(int $id): void
    {
        $this->empleadoId = $id;
        $this->showRestoreModal = true;
    }

    public function restore(): void
    {
        $empleado = Empleado::onlyTrashed()->find($this->empleadoId);
        if ($empleado) {
            $empleado->restore();
            $this->dispatch('notify', type: 'success', message: 'Empleado restaurado correctamente.');
        }
        $this->showRestoreModal = false;
        $this->empleadoId = null;
    }

    public function confirmForceDelete(int $id): void
    {
        $this->empleadoId = $id;
        $this->showForceDeleteModal = true;
    }

    public function forceDelete(): void
    {
        $empleado = Empleado::onlyTrashed()->find($this->empleadoId);
        if ($empleado) {
            $empleado->forceDelete();
            $this->dispatch('notify', type: 'success', message: 'Empleado eliminado permanentemente.');
        }
        $this->showForceDeleteModal = false;
        $this->empleadoId = null;
    }

    // Render
    public function render()
    {
        return view('hr::livewire.empleado-manager');
    }
}
