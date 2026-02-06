<?php

namespace Modules\Core\Livewire;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Core\Models\Permission;

class PermissionManager extends Component
{
    use WithPagination;

    // ─────────────────────────────────────────────────────────────
    // Propiedades de búsqueda y filtros
    // ─────────────────────────────────────────────────────────────
    
    #[Url(as: 'q')]
    public string $search = '';
    
    #[Url]
    public string $moduleFilter = '';
    
    public string $sortField = 'module';
    public string $sortDirection = 'asc';
    
    public int $perPage = 25;

    // ─────────────────────────────────────────────────────────────
    // Propiedades computadas
    // ─────────────────────────────────────────────────────────────
    
    #[Computed]
    public function permissions()
    {
        return Permission::query()
            ->withCount('roles')
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('display_name', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%");
            }))
            ->when($this->moduleFilter, fn ($q) => $q->where('module', $this->moduleFilter))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    #[Computed]
    public function permissionsByModule()
    {
        return Permission::query()
            ->withCount('roles')
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('display_name', 'like', "%{$this->search}%");
            }))
            ->when($this->moduleFilter, fn ($q) => $q->where('module', $this->moduleFilter))
            ->orderBy('module')
            ->orderBy('name')
            ->get()
            ->groupBy('module');
    }

    #[Computed]
    public function modules()
    {
        return Permission::select('module')
            ->distinct()
            ->orderBy('module')
            ->pluck('module');
    }

    #[Computed]
    public function totalPermissions()
    {
        return Permission::count();
    }

    #[Computed]
    public function permissionsPerModule()
    {
        return Permission::selectRaw('module, count(*) as count')
            ->groupBy('module')
            ->orderBy('module')
            ->pluck('count', 'module');
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

    public function updatedModuleFilter(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'moduleFilter']);
        $this->resetPage();
    }

    public function filterByModule(string $module): void
    {
        $this->moduleFilter = $this->moduleFilter === $module ? '' : $module;
        $this->resetPage();
    }

    // ─────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────
    
    /**
     * Obtener el color del badge según el módulo
     */
    public function getModuleBadgeColor(string $module): string
    {
        return match ($module) {
            'core' => 'info',
            'erp' => 'primary',
            'hr' => 'dark',
            'crm' => 'success',
            'fms' => 'warning',
            'reports' => 'secondary',
            'partners' => 'danger',
            default => 'light',
        };
    }

    /**
     * Obtener el icono según el módulo
     */
    public function getModuleIcon(string $module): string
    {
        return match ($module) {
            'core' => 'tabler-settings',
            'erp' => 'tabler-building-store',
            'hr' => 'tabler-users-group',
            'crm' => 'tabler-address-book',
            'fms' => 'tabler-calculator',
            'reports' => 'tabler-chart-bar',
            'partners' => 'tabler-building',
            default => 'tabler-puzzle',
        };
    }

    /**
     * Obtener el nombre formateado del módulo
     */
    public function getModuleDisplayName(string $module): string
    {
        return match ($module) {
            'core' => 'Core (Sistema)',
            'erp' => 'ERP',
            'hr' => 'Recursos Humanos',
            'crm' => 'CRM',
            'fms' => 'Finanzas (FMS)',
            'reports' => 'Reportes',
            'partners' => 'Partners',
            default => ucfirst($module),
        };
    }

    // ─────────────────────────────────────────────────────────────
    // Render
    // ─────────────────────────────────────────────────────────────
    
    public function render()
    {
        return view('core::livewire.permission-manager');
    }
}
