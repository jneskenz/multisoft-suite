<?php

namespace Modules\HR\Livewire;

use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\HR\Models\Departamento;
use Modules\HR\Models\Empleado;
use Modules\HR\Models\TipoDepartamento;

class DepartamentoManager extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $statusFilter = '';

    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 10;

    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public bool $isEditing = false;
    public ?int $departamentoId = null;

    public ?int $tipo_departamento_id = null;
    public ?int $padre_id = null;
    public ?int $jefe_id = null;
    public string $codigo = '';
    public string $name = '';
    public string $descripcion = '';
    public int $estado = Departamento::ESTADO_ACTIVO;

    public bool $showRestoreModal = false;
    public bool $showForceDeleteModal = false;
    public bool $showTrashedDepartamentos = false;

    protected array $allowedSortFields = [
        'id',
        'name',
        'codigo',
        'estado',
        'created_at',
    ];

    #[Computed]
    public function departamentos()
    {
        $query = Departamento::query()
            ->with(['tipoDepartamento:id,name', 'padre:id,name', 'jefe:id,nombre'])
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('codigo', 'like', "%{$this->search}%")
                        ->orWhere('descripcion', 'like', "%{$this->search}%");
                });
            })
            ->when($this->statusFilter !== '', function ($q) {
                if ($this->statusFilter === 'active') {
                    $q->where('estado', Departamento::ESTADO_ACTIVO);
                } elseif ($this->statusFilter === 'inactive') {
                    $q->where('estado', Departamento::ESTADO_INACTIVO);
                }
            })
            ->orderBy($this->sortField, $this->sortDirection);

        if ($this->showTrashedDepartamentos) {
            $query->onlyTrashed();
        }

        return $query->paginate($this->perPage);
    }

    #[Computed]
    public function tiposDepartamento()
    {
        return TipoDepartamento::query()
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    #[Computed]
    public function padresDisponibles()
    {
        return Departamento::query()
            ->when($this->departamentoId, fn ($q) => $q->where('id', '!=', $this->departamentoId))
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    #[Computed]
    public function jefesDisponibles()
    {
        return Empleado::query()
            ->orderBy('nombre')
            ->get(['id', 'nombre']);
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'codigo' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('hr_departamentos', 'codigo')->ignore($this->departamentoId),
            ],
            'descripcion' => ['nullable', 'string'],
            'estado' => ['required', 'integer', 'in:0,1'],
            'tipo_departamento_id' => ['nullable', 'exists:hr_tipo_departamentos,id'],
            'padre_id' => ['nullable', 'exists:hr_departamentos,id'],
            'jefe_id' => ['nullable', 'exists:hr_empleados,id'],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'codigo.unique' => 'Este codigo ya existe.',
            'tipo_departamento_id.exists' => 'El tipo seleccionado no es valido.',
            'padre_id.exists' => 'El departamento padre seleccionado no es valido.',
            'jefe_id.exists' => 'El jefe seleccionado no es valido.',
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
        $this->reset(['search', 'statusFilter']);
        $this->resetPage();
    }

    #[On('openCreateModal')]
    public function create(): void
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $departamento = Departamento::findOrFail($id);

        $this->departamentoId = $departamento->id;
        $this->tipo_departamento_id = $departamento->tipo_departamento_id;
        $this->padre_id = $departamento->padre_id;
        $this->jefe_id = $departamento->jefe_id;
        $this->codigo = $departamento->codigo ?? '';
        $this->name = $departamento->name;
        $this->descripcion = $departamento->descripcion ?? '';
        $this->estado = (int) $departamento->estado;
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'tipo_departamento_id' => $this->tipo_departamento_id,
            'padre_id' => $this->padre_id,
            'jefe_id' => $this->jefe_id,
            'codigo' => $this->codigo ?: null,
            'name' => $this->name,
            'descripcion' => $this->descripcion ?: null,
            'estado' => $this->estado,
        ];

        if ($this->departamentoId && $this->padre_id === $this->departamentoId) {
            $data['padre_id'] = null;
        }

        if ($this->isEditing) {
            $departamento = Departamento::findOrFail($this->departamentoId);
            $departamento->update($data);
            $message = __('Departamento actualizado correctamente.');
        } else {
            Departamento::create($data);
            $message = __('Departamento creado correctamente.');
        }

        $this->closeModal();
        $this->dispatch('notify', type: 'success', message: $message);
    }

    public function confirmDelete(int $id): void
    {
        $this->departamentoId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        $departamento = Departamento::findOrFail($this->departamentoId);
        $departamento->delete();

        $this->showDeleteModal = false;
        $this->departamentoId = null;
        $this->dispatch('notify', type: 'success', message: 'Departamento eliminado correctamente.');
    }

    public function toggleEstado(int $id): void
    {
        $departamento = Departamento::findOrFail($id);

        $nuevoEstado = (int) $departamento->estado === Departamento::ESTADO_ACTIVO
            ? Departamento::ESTADO_INACTIVO
            : Departamento::ESTADO_ACTIVO;

        $departamento->update(['estado' => $nuevoEstado]);

        $mensaje = $nuevoEstado === Departamento::ESTADO_ACTIVO
            ? 'Departamento activado correctamente.'
            : 'Departamento inactivado correctamente.';

        $this->dispatch('notify', type: 'success', message: $mensaje);
    }

    public function toggleTrashedDepartamentos(): void
    {
        $this->showTrashedDepartamentos = !$this->showTrashedDepartamentos;
        $this->resetPage();
    }

    public function confirmRestore(int $id): void
    {
        $this->departamentoId = $id;
        $this->showRestoreModal = true;
    }

    public function restore(): void
    {
        $departamento = Departamento::onlyTrashed()->find($this->departamentoId);

        if ($departamento) {
            $departamento->restore();
            $this->dispatch('notify', type: 'success', message: 'Departamento restaurado correctamente.');
        }

        $this->showRestoreModal = false;
        $this->departamentoId = null;
    }

    public function confirmForceDelete(int $id): void
    {
        $this->departamentoId = $id;
        $this->showForceDeleteModal = true;
    }

    public function forceDelete(): void
    {
        $departamento = Departamento::onlyTrashed()->find($this->departamentoId);

        if ($departamento) {
            $departamento->forceDelete();
            $this->dispatch('notify', type: 'success', message: 'Departamento eliminado permanentemente.');
        }

        $this->showForceDeleteModal = false;
        $this->departamentoId = null;
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
            'departamentoId',
            'tipo_departamento_id',
            'padre_id',
            'jefe_id',
            'codigo',
            'name',
            'descripcion',
            'estado',
        ]);

        $this->estado = Departamento::ESTADO_ACTIVO;
        $this->resetValidation();
    }

    public function render()
    {
        return view('hr::livewire.departamento-manager');
    }
}
