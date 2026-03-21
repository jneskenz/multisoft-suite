<?php

namespace Modules\HR\Livewire;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Core\Models\GroupCompany;
use Modules\Core\Models\Company;
use Modules\Core\Models\Location;
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
    public string $nombres = '';
    public string $apellidos = '';
    public string $email = '';
    public int $estado = 1;
    public string $documento_tipo = '';
    public string $documento_numero = '';
    public string $telefono = '';
    public string $direccion = '';
    public string $estado_civil = '';
    public string $genero = '';
    public string $fecha_nacimiento = '';
    public string $codigo_empleado = '';
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
                $q->where('nombres', 'like', "%{$this->search}%")
                    ->orWhere('apellidos', 'like', "%{$this->search}%")
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
            'nombres' => ['required', 'string', 'min:3', 'max:255'],
            'apellidos' => ['required', 'string', 'min:3', 'max:255'],
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
            'direccion' => ['nullable', 'string', 'max:255'],
            'estado_civil' => ['nullable', 'string', 'max:20'],
            'genero' => ['nullable', 'string', 'max:20'],
            'fecha_nacimiento' => ['nullable', 'date'],
            'codigo_empleado' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('hr_empleados', 'codigo_empleado')->ignore($this->empleadoId)
            ],
            'fecha_ingreso' => ['nullable', 'date'],
            'company_id' => ['required', 'exists:core_companies,id'],
            'location_id' => ['nullable', 'exists:core_locations,id'],
        ];
        return $rules;
    }

    protected function messages(): array
    {
        return [
            'nombres.required' => 'El nombre es obligatorio.',
            'apellidos.required' => 'El apellido es obligatorio.',
            'nombres.min' => 'El nombre debe tener al menos 3 caracteres.',
            'apellidos.min' => 'El apellido debe tener al menos 3 caracteres.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'fecha_nacimiento.date' => 'La fecha de nacimiento no es válida.',
            'direccion.required' => 'La dirección es obligatoria.',
            'estado_civil.required' => 'El estado civil es obligatorio.',
            'genero.required' => 'El género es obligatorio.',
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
        $this->nombres = $empleado->nombres;
        $this->apellidos = $empleado->apellidos;
        $this->email = $empleado->email ?? '';
        $this->estado = $empleado->estado ?? Empleado::ESTADO_ACTIVO;
        $this->documento_tipo = $empleado->documento_tipo ?? '';
        $this->documento_numero = $empleado->documento_numero ?? '';
        $this->telefono = $empleado->telefono ?? '';
        $this->direccion = $empleado->direccion ?? '';
        $this->estado_civil = $empleado->estado_civil ?? '';
        $this->genero = $empleado->genero ?? '';
        $this->fecha_nacimiento = $empleado->fecha_nacimiento?->format('Y-m-d') ?? '';
        $this->codigo_empleado = $empleado->codigo_empleado ?? '';
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
        $message = '';

        $data = [
            'nombres' => $this->nombres,
            'apellidos' => $this->apellidos,
            'email' => $this->email ?: null,
            'estado' => $this->estado,
            'documento_tipo' => $this->documento_tipo ?: null,
            'documento_numero' => $this->documento_numero ?: null,
            'telefono' => $this->telefono ?: null,
            'direccion' => $this->direccion ?: null,
            'estado_civil' => $this->estado_civil ?: null,
            'genero' => $this->genero ?: null,
            'fecha_nacimiento' => $this->fecha_nacimiento ?: null,
            'codigo_empleado' => $this->codigo_empleado ?: null,
            'fecha_ingreso' => $this->fecha_ingreso ?: null,
            'company_id' => $this->company_id,
            'location_id' => $this->location_id,
        ];

        DB::transaction(function () use ($data, $group, &$message) {
            if ($this->isEditing) {
                $empleado = Empleado::findOrFail($this->empleadoId);
                $empleado->update($data);
                $message = __('Empleado actualizado correctamente.');
            } else {
                if ($group) {
                    $data['tenant_id'] = $group->tenant_id;
                    $data['group_company_id'] = $group->id;
                }
                // Generar código de empleado automático
                $lastCode = Empleado::where('group_company_id', $group?->id)
                    ->whereNotNull('codigo_empleado')
                    ->max('codigo_empleado');
                $nextNumber = 1;
                if ($lastCode && preg_match('/(\d+)$/', $lastCode, $m)) {
                    $nextNumber = (int) $m[1] + 1;
                }
                $data['codigo_empleado'] = 'EMP-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
                Empleado::create($data);
                $message = __('Empleado creado correctamente.');
            }
        });

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

    // Búsqueda de datos por DNI
    public function searchDni(): void
    {
        // Solo buscar si está en modo creación (no edición) y si seleccionó DNI
        if ($this->isEditing || $this->documento_tipo !== 'DNI' || !$this->documento_numero) {
            return;
        }

        // Validar formato DNI (8 dígitos)
        if (!preg_match('/^\d{8}$/', $this->documento_numero)) {
            $this->addError('documento_numero', 'El DNI debe tener exactamente 8 dígitos numéricos.');
            return;
        }

        // Verificar que el DNI no esté ya registrado
        if (Empleado::where('documento_numero', $this->documento_numero)
            ->where('id', '!=', $this->empleadoId ?? 0)
            ->exists()) {
            $this->addError('documento_numero', 'Este número de DNI ya está registrado en el sistema.');
            return;
        }

        // Consultar la API de DNI
        $resultado = lookup_dni($this->documento_numero);

        if (!$resultado['success']) {
            $this->addError('documento_numero', $resultado['message']);
            return;
        }

        $datos = $resultado['data'];

        // Rellenar los campos automáticamente
        if (!empty($datos['nombres'])) {
            $this->nombres = $datos['nombres'];
        }

        if (!empty($datos['apellido_paterno']) || !empty($datos['apellido_materno'])) {
            $apellidos = trim(
                ($datos['apellido_paterno'] ?? '') . ' ' .
                ($datos['apellido_materno'] ?? '')
            );
            $this->apellidos = $apellidos ?: $this->apellidos;
        }

        // Notificar éxito
        $this->dispatch('notify', type: 'success', message: 'Datos del DNI cargados correctamente.');
    }

    /**
     * Listener que se ejecuta cuando cambia el campo documento_numero
     * Solo busca si es DNI con 8 dígitos
     */
    public function updatedDocumentoNumero(): void
    {
        // Limpiar errores previos
        $this->resetErrorBag('documento_numero');

        // Buscar automáticamente después de completar 8 dígitos
        if ($this->documento_tipo === 'DNI' && strlen($this->documento_numero) === 8) {
            $this->searchDni();
        }
    }

    /**
     * Listener que se ejecuta cuando cambia el tipo de documento.
     * Solo limpiamos errores — NO tocamos el número ya ingresado para evitar
     * que el morph de Livewire lo borre mientras el usuario está tipeando.
     */
    public function updatedDocumentoTipo(): void
    {
        $this->resetErrorBag('documento_numero');
    }

    private function resetForm(): void
    {
        $this->reset([
            'empleadoId', 'nombres', 'apellidos', 'email', 'estado',
            'documento_tipo', 'documento_numero', 'telefono',
            'direccion', 'estado_civil', 'genero', 'fecha_nacimiento',
            'codigo_empleado', 'fecha_ingreso',
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
