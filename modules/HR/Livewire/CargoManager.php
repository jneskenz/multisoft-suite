<?php

namespace Modules\HR\Livewire;

use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\HR\Models\Cargo;
use Modules\HR\Models\Departamento;

class CargoManager extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $statusFilter = '';

    #[Url]
    public string $departamentoFilter = '';

    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 10;

    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public bool $isEditing = false;
    public ?int $cargoId = null;

    public ?int $departamento_id = null;
    public string $codigo = '';
    public string $name = '';
    public string $descripcion = '';
    public string $nivel = '';
    public int $estado = Cargo::ESTADO_ACTIVO;

    public bool $showRestoreModal = false;
    public bool $showForceDeleteModal = false;
    public bool $showTrashedCargos = false;

    protected array $allowedSortFields = [
        'id',
        'name',
        'codigo',
        'nivel',
        'estado',
        'created_at',
    ];

    #[Computed]
    public function cargos()
    {
        $query = Cargo::query()
            ->with('departamento:id,name')
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('codigo', 'like', "%{$this->search}%")
                        ->orWhere('descripcion', 'like', "%{$this->search}%")
                        ->orWhere('nivel', 'like', "%{$this->search}%");
                });
            })
            ->when($this->statusFilter !== '', function ($q) {
                if ($this->statusFilter === 'active') {
                    $q->where('estado', Cargo::ESTADO_ACTIVO);
                } elseif ($this->statusFilter === 'inactive') {
                    $q->where('estado', Cargo::ESTADO_INACTIVO);
                }
            })
            ->when($this->departamentoFilter !== '', function ($q) {
                $q->where('departamento_id', (int) $this->departamentoFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection);

        if ($this->showTrashedCargos) {
            $query->onlyTrashed();
        }

        return $query->paginate($this->perPage);
    }

    #[Computed]
    public function departamentos()
    {
        return Departamento::query()
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    protected function rules(): array
    {
        return [
            'departamento_id' => ['required', 'exists:hr_departamentos,id'],
            'name' => ['required', 'string', 'max:100'],
            'codigo' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('hr_cargos', 'codigo')
                    ->where(fn ($q) => $q->where('departamento_id', $this->departamento_id))
                    ->ignore($this->cargoId),
            ],
            'descripcion' => ['nullable', 'string'],
            'nivel' => ['nullable', 'string', 'max:50'],
            'estado' => ['required', 'integer', 'in:0,1'],
        ];
    }

    protected function messages(): array
    {
        return [
            'departamento_id.required' => 'El departamento es obligatorio.',
            'departamento_id.exists' => 'El departamento seleccionado no es valido.',
            'name.required' => 'El nombre del cargo es obligatorio.',
            'codigo.unique' => 'Este codigo ya existe para el departamento seleccionado.',
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
        $this->reset(['search', 'statusFilter', 'departamentoFilter']);
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
        $cargo = Cargo::findOrFail($id);

        $this->cargoId = $cargo->id;
        $this->departamento_id = $cargo->departamento_id;
        $this->codigo = $cargo->codigo ?? '';
        $this->name = $cargo->name;
        $this->descripcion = $cargo->descripcion ?? '';
        $this->nivel = $cargo->nivel ?? '';
        $this->estado = (int) $cargo->estado;
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'departamento_id' => $this->departamento_id,
            'codigo' => $this->codigo ?: null,
            'name' => $this->name,
            'descripcion' => $this->descripcion ?: null,
            'nivel' => $this->nivel ?: null,
            'estado' => $this->estado,
        ];

        if ($this->isEditing) {
            $cargo = Cargo::findOrFail($this->cargoId);
            $cargo->update($data);
            $message = __('Cargo actualizado correctamente.');
        } else {
            Cargo::create($data);
            $message = __('Cargo creado correctamente.');
        }

        $this->closeModal();
        $this->dispatch('notify', type: 'success', message: $message);
    }

    public function confirmDelete(int $id): void
    {
        $this->cargoId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        $cargo = Cargo::findOrFail($this->cargoId);
        $cargo->delete();

        $this->showDeleteModal = false;
        $this->cargoId = null;
        $this->dispatch('notify', type: 'success', message: 'Cargo eliminado correctamente.');
    }

    public function toggleEstado(int $id): void
    {
        $cargo = Cargo::findOrFail($id);

        $nuevoEstado = (int) $cargo->estado === Cargo::ESTADO_ACTIVO
            ? Cargo::ESTADO_INACTIVO
            : Cargo::ESTADO_ACTIVO;

        $cargo->update(['estado' => $nuevoEstado]);

        $mensaje = $nuevoEstado === Cargo::ESTADO_ACTIVO
            ? 'Cargo activado correctamente.'
            : 'Cargo inactivado correctamente.';

        $this->dispatch('notify', type: 'success', message: $mensaje);
    }

    public function toggleTrashedCargos(): void
    {
        $this->showTrashedCargos = !$this->showTrashedCargos;
        $this->resetPage();
    }

    public function confirmRestore(int $id): void
    {
        $this->cargoId = $id;
        $this->showRestoreModal = true;
    }

    public function restore(): void
    {
        $cargo = Cargo::onlyTrashed()->find($this->cargoId);

        if ($cargo) {
            $cargo->restore();
            $this->dispatch('notify', type: 'success', message: 'Cargo restaurado correctamente.');
        }

        $this->showRestoreModal = false;
        $this->cargoId = null;
    }

    public function confirmForceDelete(int $id): void
    {
        $this->cargoId = $id;
        $this->showForceDeleteModal = true;
    }

    public function forceDelete(): void
    {
        $cargo = Cargo::onlyTrashed()->find($this->cargoId);

        if ($cargo) {
            $cargo->forceDelete();
            $this->dispatch('notify', type: 'success', message: 'Cargo eliminado permanentemente.');
        }

        $this->showForceDeleteModal = false;
        $this->cargoId = null;
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
            'cargoId',
            'departamento_id',
            'codigo',
            'name',
            'descripcion',
            'nivel',
            'estado',
        ]);

        $this->estado = Cargo::ESTADO_ACTIVO;
        $this->resetValidation();
    }

    public function render()
    {
        return view('hr::livewire.cargo-manager');
    }
}
