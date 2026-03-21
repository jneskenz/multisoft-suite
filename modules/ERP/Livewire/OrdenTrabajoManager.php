<?php

namespace Modules\ERP\Livewire;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Core\Contracts\PatientDirectoryContract;
use Modules\ERP\Models\OrdenTrabajo;

class OrdenTrabajoManager extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    #[Url(as: 'ot')]
    public string $ordenFilter = '';

    #[Url(as: 'paciente')]
    public string $patientFilter = '';

    #[Url(as: 'estado')]
    public string $estadoFilter = '';

    #[Url(as: 'prioridad')]
    public string $prioridadFilter = '';

    #[Url(as: 'fecha')]
    public string $fechaFilter = '';

    #[Url(as: 'pp')]
    public int $perPage = 15;

    #[Url(as: 'sort')]
    public string $sortField = 'fecha_orden';

    #[Url(as: 'dir')]
    public string $sortDirection = 'desc';

    private const PER_PAGE_OPTIONS = [10, 15, 25, 50];
    private const ESTADOS_VALIDOS = ['pendiente', 'en_laboratorio', 'control_calidad', 'listo', 'entregado', 'anulado'];
    private const PRIORIDADES_VALIDAS = ['normal', 'urgente'];
    private const CAMPOS_ORDENABLES = ['numero_ot', 'fecha_orden', 'fecha_prometida', 'estado_ot', 'prioridad'];

    #[Computed]
    public function hasOrdenesTrabajoTable(): bool
    {
        return Schema::hasTable('erp_ordenes_trabajo');
    }

    #[Computed]
    public function hasActiveFilters(): bool
    {
        return trim($this->ordenFilter) !== ''
            || trim($this->patientFilter) !== ''
            || trim($this->estadoFilter) !== ''
            || trim($this->prioridadFilter) !== ''
            || trim($this->fechaFilter) !== '';
    }

    #[Computed]
    public function stats(): array
    {
        if (!$this->hasOrdenesTrabajoTable) {
            return ['hoy' => 0, 'pendiente' => 0, 'en_laboratorio' => 0, 'listo' => 0, 'total' => 0];
        }

        $baseQuery = $this->construirConsultaBase();

        return [
            'hoy' => (clone $baseQuery)->whereDate('fecha_orden', now()->toDateString())->count(),
            'pendiente' => (clone $baseQuery)->where('estado_ot', 'pendiente')->count(),
            'en_laboratorio' => (clone $baseQuery)->where('estado_ot', 'en_laboratorio')->count(),
            'listo' => (clone $baseQuery)->where('estado_ot', 'listo')->count(),
            'total' => (clone $baseQuery)->count(),
        ];
    }

    #[Computed]
    public function ordenesTrabajo(): LengthAwarePaginator
    {
        if (!$this->hasOrdenesTrabajoTable) {
            return new LengthAwarePaginator(collect(), 0, $this->porPaginaResuelta(), 1, [
                'path' => request()->url(),
                'query' => request()->query(),
            ]);
        }

        $query = $this->construirConsultaBase();
        $this->aplicarFiltros($query);

        return $query
            ->orderBy($this->campoOrdenamientoResuelto(), $this->direccionOrdenamientoResuelta())
            ->paginate($this->porPaginaResuelta());
    }

    #[Computed]
    public function patientOptions(): array
    {
        [$tenantId, $groupCompanyId] = $this->contextoActual();

        $term = trim($this->patientFilter);
        if ($term !== '' && mb_strlen($term) < 2) {
            return [];
        }

        return app(PatientDirectoryContract::class)->search(
            tenantId: $tenantId,
            groupCompanyId: $groupCompanyId,
            term: $term,
            limit: 15
        );
    }

    public function updatedOrdenFilter(): void { $this->resetPage(); }
    public function updatedPatientFilter(): void { $this->resetPage(); }
    public function updatedEstadoFilter(): void { $this->resetPage(); }
    public function updatedPrioridadFilter(): void { $this->resetPage(); }
    public function updatedFechaFilter(): void { $this->resetPage(); }

    public function updatedPerPage(mixed $value): void
    {
        $perPage = (int) $value;
        $this->perPage = in_array($perPage, self::PER_PAGE_OPTIONS, true) ? $perPage : 15;
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if (!in_array($field, self::CAMPOS_ORDENABLES, true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['ordenFilter', 'patientFilter', 'estadoFilter', 'prioridadFilter', 'fechaFilter']);
        $this->resetPage();
    }

    public function render()
    {
        return view('erp::livewire.orden-trabajo-manager', [
            'estadoMeta' => [
                'pendiente' => ['label' => 'Pendiente', 'class' => 'bg-label-warning'],
                'en_laboratorio' => ['label' => 'En laboratorio', 'class' => 'bg-label-info'],
                'control_calidad' => ['label' => 'Control de calidad', 'class' => 'bg-label-primary'],
                'listo' => ['label' => 'Listo', 'class' => 'bg-label-success'],
                'entregado' => ['label' => 'Entregado', 'class' => 'bg-label-dark'],
                'anulado' => ['label' => 'Anulado', 'class' => 'bg-label-danger'],
            ],
            'prioridadMeta' => [
                'normal' => ['label' => 'Normal', 'class' => 'bg-label-secondary'],
                'urgente' => ['label' => 'Urgente', 'class' => 'bg-label-danger'],
            ],
        ]);
    }

    private function construirConsultaBase(): Builder
    {
        [$tenantId, $groupCompanyId] = $this->contextoActual();

        $query = OrdenTrabajo::query()
            ->with([
                'ticket:id,ticket_numero',
                'receta:id,receta_numero',
                'paciente:id,nombre_completo,nombres,apellido_paterno,apellido_materno,tipo_documento,numero_documento',
            ])
            ->withCount('detalles')
            ->tenant($tenantId)
            ->activos();

        if ($groupCompanyId !== null) {
            $query->where(function (Builder $scope) use ($groupCompanyId): void {
                $scope->where('group_company_id', $groupCompanyId)
                    ->orWhereNull('group_company_id');
            });
        }

        return $query;
    }

    private function aplicarFiltros(Builder $query): void
    {
        $ordenFilter = trim($this->ordenFilter);
        if ($ordenFilter !== '') {
            $query->where('numero_ot', 'like', '%' . $this->escaparLike($ordenFilter) . '%');
        }

        $patientFilter = trim($this->patientFilter);
        if ($patientFilter !== '') {
            $terminos = $this->resolverTerminosPaciente($patientFilter);
            $query->whereHas('paciente', function (Builder $patientQuery) use ($terminos): void {
                $patientQuery->where(function (Builder $scope) use ($terminos): void {
                    foreach ($terminos as $index => $term) {
                        $like = '%' . $this->escaparLike($term) . '%';

                        if ($index === 0) {
                            $scope->where('nombre_completo', 'like', $like)
                                ->orWhere('nombres', 'like', $like)
                                ->orWhere('numero_documento', 'like', $like);
                            continue;
                        }

                        $scope->orWhere('nombre_completo', 'like', $like)
                            ->orWhere('nombres', 'like', $like)
                            ->orWhere('numero_documento', 'like', $like);
                    }
                });
            });
        }

        $estadoFilter = trim($this->estadoFilter);
        if ($estadoFilter !== '' && in_array($estadoFilter, self::ESTADOS_VALIDOS, true)) {
            $query->where('estado_ot', $estadoFilter);
        }

        $prioridadFilter = trim($this->prioridadFilter);
        if ($prioridadFilter !== '' && in_array($prioridadFilter, self::PRIORIDADES_VALIDAS, true)) {
            $query->where('prioridad', $prioridadFilter);
        }

        $fechaFilter = trim($this->fechaFilter);
        if ($fechaFilter !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaFilter) === 1) {
            $query->whereDate('fecha_orden', $fechaFilter);
        }
    }

    private function escaparLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }

    /**
     * @return array<int,string>
     */
    private function resolverTerminosPaciente(string $value): array
    {
        $value = trim(preg_replace('/\s+/', ' ', $value) ?? $value);
        if ($value === '') {
            return [];
        }

        $terminos = [$value];

        if (str_contains($value, '(')) {
            $nombre = trim((string) strtok($value, '('));
            if ($nombre !== '') {
                $terminos[] = $nombre;
            }
        }

        if (preg_match('/\(([^)]*)\)/', $value, $matches) === 1) {
            $inside = trim((string) ($matches[1] ?? ''));
            if ($inside !== '') {
                $terminos[] = $inside;

                foreach (preg_split('/\s+/', $inside) ?: [] as $token) {
                    $token = trim($token);
                    if ($token !== '' && mb_strlen($token) >= 3) {
                        $terminos[] = $token;
                    }
                }
            }
        }

        return array_values(array_unique($terminos));
    }

    /**
     * @return array{0:int,1:int|null}
     */
    private function contextoActual(): array
    {
        $group = current_group();
        $tenantId = (int) ($group?->tenant_id ?? auth()->user()?->tenant_id ?? 0);
        $groupCompanyId = $group?->id ? (int) $group->id : null;

        abort_if($tenantId <= 0, 403, 'No hay contexto de tenant activo.');

        return [$tenantId, $groupCompanyId];
    }

    private function campoOrdenamientoResuelto(): string
    {
        return in_array($this->sortField, self::CAMPOS_ORDENABLES, true) ? $this->sortField : 'fecha_orden';
    }

    private function direccionOrdenamientoResuelta(): string
    {
        return $this->sortDirection === 'asc' ? 'asc' : 'desc';
    }

    private function porPaginaResuelta(): int
    {
        return in_array($this->perPage, self::PER_PAGE_OPTIONS, true) ? $this->perPage : 15;
    }
}
